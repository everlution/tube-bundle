<?php

namespace Everlution\TubeBundle\Provider;

use Everlution\TubeBundle\Adapter\AdapterInterface;
use Everlution\TubeBundle\Model\Interfaces\JobInterface;
use Everlution\TubeBundle\Exception as TubeException;
use Everlution\TubeBundle\Model\Traits\JobFeaturesTrait;
use Everlution\TubeBundle\Event\TubeEvents;
use Everlution\TubeBundle\EventDispatcher\JobEvent;
use Everlution\TubeBundle\EventDispatcher\DefaultEvent;
use Everlution\TubeBundle\Event\JobEvents;

abstract class AbstractTubeProvider implements TubeProviderInterface
{
    private $adapter;

    private $tubeName;

    private $eventDispatcher;

    use JobFeaturesTrait;

    public function __construct(AdapterInterface $adapter, $queueName, $eventDispatcher)
    {
        $this->adapter = $adapter;
        $this->tubeName = $queueName;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getTubeName()
    {
        return $this->tubeName;
    }

    private function initJob(JobInterface $job)
    {
        if (!$job->getDelay()) {
            $job->setDelay($this->getDelay());
        }

        if (!$job->getDelayOnRetry()) {
            $job->setDelayOnRetry($this->getDelayOnRetry());
        }

        if (!$job->getMaxRetriesOnFailure()) {
            $job->setMaxRetriesOnFailure($this->getMaxRetriesOnFailure());
        }

        if (!$job->getPriority()) {
            $job->setPriority($this->getPriority());
        }

        if (!$job->getTtr()) {
            $job->setTtr($this->getTtr());
        }
    }

    public function produce(JobInterface $job)
    {
        if ($this->isStopped()) {
            return false;
        }

        $this->initJob($job);

        try {
            $this->validateJob($job);

            $this
                ->adapter
                ->produce($this->tubeName, $job)
            ;

            $this
                ->eventDispatcher
                ->dispatch(
                    JobEvents::PRODUCED,
                    new JobEvent($job)
                )
            ;
        } catch (TubeException\ServiceDownException $e) {
            $this
                ->eventDispatcher
                ->dispatch(
                    TubeEvents::SERVICE_DOWN,
                    new DefaultEvent()
                )
            ;
            throw $e;
        } catch (TubeException\InvalidJobException $e) {
            $this
                ->eventDispatcher
                ->dispatch(
                    JobEvents::INVALID,
                    new DefaultEvent()
                )
            ;
            throw $e;
        }
    }

    public function consumeNext()
    {
        if ($this->isStopped()) {
            return false;
        }

        try {
            /* @var $job \Everlution\TubeBundle\Model\Interfaces\JobInterface */
            $job = $this
                ->adapter
                ->reserve($this->tubeName)
            ;

            $this->validateJob($job);

            $this->consumeOne($job);

            $this
                ->eventDispatcher
                ->dispatch(
                    JobEvents::CONSUMED,
                    new JobEvent($job)
                )
            ;

            $this
                ->adapter
                ->delete($this->tubeName, $job)
            ;
        } catch (\Exception $e) {
            $this
                ->eventDispatcher
                ->dispatch(
                    JobEvents::FAILED,
                    new JobEvent($job, $e->getMessage())
                )
            ;

            $retries = $this
                ->adapter
                ->countRetries($job)
            ;

            if ($retries <= $job->getMaxRetriesOnFailure()) {
                $this
                    ->adapter
                    ->release(
                        $this->tubeName,
                        $job
                    )
                ;
            } else {
                $this
                    ->adapter
                    ->bury(
                        $this->tubeName,
                        $job
                    )
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
}
