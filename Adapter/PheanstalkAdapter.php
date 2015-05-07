<?php

namespace Everlution\TubeBundle\Adapter;

use Everlution\TubeBundle\Serializer\JobSerializerInterface;
use Pheanstalk\Pheanstalk;
use Everlution\TubeBundle\Model\Interfaces\JobInterface;
use Everlution\TubeBundle\Exception\ServiceDownException;

class PheanstalkAdapter extends AbstractAdapter
{
    const DEFAULT_TUBE = 'default';

    private $pheanstalk;

    public function __construct(JobSerializerInterface $jobSerializer, Pheanstalk $pheanstalk)
    {
        parent::__construct($jobSerializer);
        $this->pheanstalk = $pheanstalk;
    }

    private function getTube($tubeName)
    {
        return $this
            ->pheanstalk
            ->watch($tubeName)
            ->ignore(self::DEFAULT_TUBE)
        ;
    }

    public function checkServiceUp()
    {
        $isUp = $this
            ->pheanstalk
            ->getConnection()
            ->isServiceListening()
        ;

        if (!$isUp) {
            throw new ServiceDownException(
                sprintf(
                    'Cannot reach Beanstalk %s:%s',
                    $this->pheanstalk->getConnection()->getHost(),
                    $this->pheanstalk->getConnection()->getPort()
                )
            );
        }
    }

    /**
     * produce.
     *
     * Enqueues a new job
     *
     * @param string       $tubeName
     * @param JobInterface $job
     */
    public function produce($tubeName, JobInterface $job)
    {
        $this->checkServiceUp();

        $payload = $this
            ->jobSerializer
            ->serialize($job)
        ;

        $jobId = $this
            ->pheanstalk
            ->putInTube(
                $tubeName,
                $payload,
                $job->getPriority(),
                $job->getDelay(),
                $job->getTtr()
            )
        ;

        $job->setId($jobId);
    }

    public function reserve($tubeName, $timeout = null)
    {
        $this->checkServiceUp();

        $pheanstalkJob = $this
            ->getTube($tubeName)
            ->reserveFromTube($tubeName, $timeout)
        ;

        $job = $this
            ->jobSerializer
            ->deserialize($pheanstalkJob->getData())
        ;

        $job->setId($pheanstalkJob->getId());

        return $job;
    }

    /**
     * getPheanstalkJob.
     *
     * This is mostly an hack as pheanstalk uses just the job id in order to
     * retrive the job.
     *
     * @param JobInterface $job
     *
     * @return \Pheanstalk\Job
     */
    private function getPheanstalkJob(JobInterface $job)
    {
        if (!$job->getId()) {
            throw new \Everlution\TubeBundle\Exception\InvalidJobException();
        }

        return new \Pheanstalk\Job($job->getId(), $job->getPayload());
    }

    public function release($tubeName, JobInterface $job)
    {
        $this->checkServiceUp();

        $this
            ->getTube($tubeName)
            ->release(
                $this->getPheanstalkJob($job),
                $job->getPriority(),
                $job->getDelayOnRetry()
            )
        ;
    }

    public function bury($tubeName, JobInterface $job)
    {
        $this->checkServiceUp();

        $this
            ->getTube($tubeName)
            ->bury(
                $this->getPheanstalkJob($job)
            )
        ;
    }

    public function delete($tubeName, JobInterface $job)
    {
        $this->checkServiceUp();

        $this
            ->getTube($tubeName)
            ->delete(
                $this->getPheanstalkJob($job)
            )
        ;
    }

    public function countRetries(JobInterface $job)
    {
        $this->checkServiceUp();

        $stats = $this
            ->pheanstalk
            ->statsJob(
                $this->getPheanstalkJob($job)
            )
        ;

        return $stats['releases'];
    }
}
