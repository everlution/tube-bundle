<?php

namespace Everlution\TubeBundle\Provider;

use Everlution\TubeBundle\Adapter\AdapterInterface;
use Everlution\TubeBundle\Model\Interfaces\JobInterface;
use Everlution\TubeBundle\Exception as TubeException;
use Everlution\TubeBundle\Model\Traits\JobFeaturesTrait;
use Everlution\TubeBundle\Event\TubeEvents;
use Everlution\TubeBundle\EventDispatcher\JobEvent;
use Everlution\TubeBundle\EventDispatcher\TubeEvent;
use Everlution\TubeBundle\Event\JobEvents;
use Everlution\TubeBundle\Exception\ServiceDownException;
use Everlution\TubeBundle\Manager\ManagerInterface;

abstract class AbstractTubeProvider implements TubeProviderInterface
{
    private $adapter;

    private $tubeName;

    private $manager;

    private $eventDispatcher;

    use JobFeaturesTrait;

    public function __construct(AdapterInterface $adapter, $tubeName, ManagerInterface $manager, $eventDispatcher)
    {
        $this->adapter = $adapter;
        $this->tubeName = $tubeName;
        $this->manager = $manager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getTubeName()
    {
        return $this->tubeName;
    }

    public function checkServiceUp()
    {
        try {
            $this
                ->adapter
                ->checkServiceUp()
            ;
        } catch (ServiceDownException $e) {
            $this
                ->eventDispatcher
                ->dispatch(
                    TubeEvents::SERVICE_DOWN,
                    new TubeEvent($this->tubeName, $e->getMessage())
                )
            ;
            throw $e;
        }
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
        $this->checkServiceUp();

        $this->initJob($job);

        try {
            $this->validateJob($job);

            $this
                ->adapter
                ->produce($this->tubeName, $job)
            ;
            $this
                ->eventDispatcher
                ->dispatch(JobEvents::PRODUCED, new JobEvent($this->tubeName, $job))
            ;
        } catch (TubeException\InvalidJobException $e) {
            $this
                ->eventDispatcher
                ->dispatch(JobEvents::INVALID, new JobEvent($this->tubeName, $job))
            ;
            throw $e;
        } catch (\Exception $e) {
            $this
                ->eventDispatcher
                ->dispatch(JobEvents::UNKNOWN_ERROR, new JobEvent($this->tubeName, $job))
            ;
            throw $e;
        }
    }

    public function consumeNext()
    {
        $this->checkServiceUp();

        if (!$this->isEnabled()) {
            return false;
        }

        $job = null;

        try {
            /* @var $job \Everlution\TubeBundle\Model\Interfaces\JobInterface */
            $job = $this
                ->adapter
                ->reserve($this->tubeName)
            ;

            $this
                ->eventDispatcher
                ->dispatch(JobEvents::RESERVED, new JobEvent($this->tubeName, $job))
            ;

            $this->validateJob($job);

            $this->consumeOne($job);

            $this
                ->eventDispatcher
                ->dispatch(JobEvents::CONSUMED, new JobEvent($this->tubeName, $job))
            ;

            $this
                ->adapter
                ->delete($this->tubeName, $job)
            ;

            $this
                ->eventDispatcher
                ->dispatch(JobEvents::DELETED, new JobEvent($this->tubeName, $job))
            ;
        } catch (TubeException\InvalidJobException $e) {
            $this
                ->eventDispatcher
                ->dispatch(JobEvents::INVALID, new JobEvent($this->tubeName, $job))
            ;
            $this
                ->adapter
                ->bury($this->tubeName, $job)
            ;
            $this
                ->eventDispatcher
                ->dispatch(JobEvents::BURIED, new JobEvent($this->tubeName, $job))
            ;
        } catch (TubeException\JobConsumeException $e) {
            $this
                ->eventDispatcher
                ->dispatch(JobEvents::FAILED, new JobEvent($this->tubeName, $job))
            ;
            $this
                ->adapter
                ->bury($this->tubeName, $job)
            ;
            $this
                ->eventDispatcher
                ->dispatch(JobEvents::BURIED, new JobEvent($this->tubeName, $job, $e->getMessage()))
            ;
        } catch (\Exception $e) {
            $this
                ->eventDispatcher
                ->dispatch(JobEvents::FAILED, new JobEvent($this->tubeName, $job, $e->getMessage()))
            ;

            $retries = $this
                ->adapter
                ->countJobRetries($job)
            ;

            if ($retries <= $job->getMaxRetriesOnFailure()) {
                $this
                    ->adapter
                    ->release($this->tubeName, $job)
                ;

                $this
                    ->eventDispatcher
                    ->dispatch(JobEvents::RELEASED, new JobEvent($this->tubeName, $job))
                ;
            } else {
                $this
                    ->adapter
                    ->bury($this->tubeName, $job)
                ;
                $this
                    ->eventDispatcher
                    ->dispatch(JobEvents::BURIED, new JobEvent($this->tubeName, $job, $e->getMessage()))
                ;
            }
        }
    }

    public function countJobsBuried()
    {
        $this->checkServiceUp();

        return $this
            ->adapter
            ->countJobsBuried($this->tubeName)
        ;
    }

    public function countJobsDelayed()
    {
        $this->checkServiceUp();

        return $this
            ->adapter
            ->countJobsDelayed($this->tubeName)
        ;
    }

    public function countJobsReady()
    {
        $this->checkServiceUp();

        return $this
            ->adapter
            ->countJobsReady($this->tubeName)
        ;
    }

    public function countJobsReserved()
    {
        $this->checkServiceUp();

        return $this
            ->adapter
            ->countJobsReserved($this->tubeName)
        ;
    }

    public function countJobsWaiting()
    {
        $this->checkServiceUp();

        return $this
            ->adapter
            ->countWaitingJobs($this->tubeName)
        ;
    }

    public function readNextJobReady()
    {
        $this->checkServiceUp();

        return $this
            ->adapter
            ->readNextJobReady($this->tubeName)
        ;
    }

    public function isEnabled()
    {
        return $this
            ->manager
            ->isEnabled($this->tubeName)
        ;
    }

    public function enable()
    {
        $this
            ->manager
            ->enable($this->tubeName)
        ;

        $this
            ->eventDispatcher
            ->dispatch(
                TubeEvents::ENABLED,
                new TubeEvent($this->tubeName)
            )
        ;
    }

    public function disable()
    {
        $this
            ->manager
            ->disable($this->tubeName)
        ;

        $this
            ->eventDispatcher
            ->dispatch(
                TubeEvents::DISABLED,
                new TubeEvent($this->tubeName)
            )
        ;
    }
}
