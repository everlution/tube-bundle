<?php

namespace Everlution\TubeBundle\Provider;

use Pheanstalk\Pheanstalk;
use Everlution\TubeBundle\Model\Interfaces\JobInterface;
use Everlution\TubeBundle\Exception as TubeException;
use Everlution\TubeBundle\Serializer\JobSerializerInterface;
use Everlution\TubeBundle\Model\Interfaces\JobFeaturesInterface;
use Everlution\TubeBundle\Model\Traits\JobFeaturesTrait;
use Everlution\TubeBundle\Event\TubeEvents;
use Everlution\TubeBundle\EventDispatcher\JobEvent;
use Everlution\TubeBundle\EventDispatcher\DefaultEvent;
use Everlution\TubeBundle\Event\JobEvents;

abstract class AbstractTubeProvider implements JobFeaturesInterface
{
    private $pheanstalk;

    private $tubeName;

    private $serializer;

    private $eventDispatcher;

    use JobFeaturesTrait;

    public function __construct(
        Pheanstalk $pheanstalk,
        $queueName,
        $eventDispatcher,
        JobSerializerInterface $serializer
    ) {
        $this->pheanstalk = $pheanstalk;
        $this->tubeName = $queueName;
        $this->eventDispatcher = $eventDispatcher;
        $this->serializer = $serializer;
    }

    public function getTubeName()
    {
        return $this->tubeName;
    }

    public function getTube()
    {
        return $this
            ->pheanstalk
            ->watch($this->tubeName)
            ->ignore('default')
        ;
    }

    protected function checkServiceUp()
    {
        $up = $this
            ->pheanstalk
            ->getConnection()
            ->isServiceListening()
        ;

        if (!$up) {
            $this->dispatchEvent(
                TubeEvents::SERVICE_DOWN,
                new DefaultEvent()
            );
            throw new TubeException\ServiceDownException(
                $this->pheanstalk->getConnection()->getHost(),
                $this->pheanstalk->getConnection()->getPort()
            );
        }
    }

    public function produce(JobInterface $job)
    {
        $this->checkServiceUp();

        if ($this->isStopped()) {
            return false;
        }

        $payload = $this
            ->serializer
            ->serialize($job)
        ;

        if (!$this->validateJob($job)) {
            throw new TubeException\InvalidJobException(
                $payload
            );
        }

        $jobId = $this
            ->pheanstalk
            ->putInTube(
                $this->tubeName,
                $payload,
                $job->getPriority() ? $job->getPriority() : $this->getPriority(),
                $job->getDelay() ? $job->getDelay() : $this->getDelay(),
                $job->getTtr() ? $job->getTtr() : $this->getTtr()
            )
        ;

        $job->setId($jobId);

        $this
            ->eventDispatcher
            ->dispatch(
                JobEvents::PRODUCED,
                new JobEvent($job)
            )
        ;

        return $jobId;
    }

    public function consumeNext()
    {
        $this->checkServiceUp();

        if ($this->isStopped()) {
            return false;
        }

        /* @var $pheanstalkJob \Pheanstalk\Job */
        $pheanstalkJob = $this
            ->getTube()
            ->reserve()
        ;

        /* @var $job \Everlution\TubeBundle\Model\Job */
        $job = $this
            ->serializer
            ->deserialize($pheanstalkJob->getData())
        ;

        if (!$this->validateJob($job)) {
            throw new QueueException\Job\InvalidValuesException(
                $pheanstalkJob->getData()
            );
        }

        $job->setId($pheanstalkJob->getId());

        try {
            $this->consumeOne($job);

            $this
                ->eventDispatcher
                ->dispatch(
                    JobEvents::CONSUMED,
                    new JobEvent($job)
                )
            ;

            $this
                ->getTube()
                ->delete($pheanstalkJob)
            ;
        } catch (\Exception $e) {
            $this
                ->eventDispatcher
                ->dispatch(
                    JobEvents::FAILED,
                    new JobEvent($job, $e->getMessage())
                )
            ;

            $stats = $this
                ->getTube()
                ->statsJob($job)
            ;

            $maxRetriesOnFailure = $job->getMaxRetriesOnFailure() ?
                $job->getMaxRetriesOnFailure() :
                $this->getMaxRetriesOnFailure()
            ;

            if ($stats['releases'] <= $maxRetriesOnFailure) {
                $this
                    ->getTube()
                    ->release(
                        $pheanstalkJob,
                        $job->getPriority() ? $job->getPriority() : $this->getPriority(),
                        $job->getDelayOnRetry() ? $job->getDelayOnRetry() : $this->getDelayOnRetry()
                    )
                ;
            } else {
                $this
                    ->getTube()
                    ->bury($pheanstalkJob)
                ;
                $this
                    ->eventDispatcher
                    ->dispatch(
                        JobEvents::DISCARDED,
                        new JobEvent($job, $e->getMessage())
                    )
                ;
            }
        }
    }

    final public function isRunning()
    {
        return !$this->isStopped();
    }

    /**
     * isStopped.
     *
     * This method is used for safely stopping a queue.
     * The idea is to persist the status of the tube somewhere (database, redis, etc.)
     * and depending by that value return true/false.
     * Once the tube gets stopped this will prevent to produce/consume any other
     * job but it will allow the current one to finish.
     *
     * @return bool
     */
    abstract public function isStopped();

    /**
     * stop.
     *
     * Stops the produce/consume actions persisting the status somewhere.
     */
    abstract public function stop();

    /**
     * start.
     *
     * Starts the produce/consume actions persisting the status somewhere.
     */
    abstract public function start();

    /**
     * validateJob.
     *
     * This method is in charge of validating the payload of the job.
     *
     * @return bool
     */
    abstract public function validateJob(JobInterface $job);

    /**
     * consumeOne.
     *
     * This method contains the logic on how to consume a job in this tube.
     * It's called in a loop by consumeNext().
     *
     * No need to return any value, so please throw exceptions if any error
     * happens.
     */
    abstract public function consumeOne(JobInterface $job);
}
