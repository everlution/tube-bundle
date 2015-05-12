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

    public function __construct(JobSerializerInterface $jobSerializer, $prefix, Pheanstalk $pheanstalk)
    {
        parent::__construct($jobSerializer, $prefix);
        $this->pheanstalk = $pheanstalk;
    }

    private function getTube($tubeName)
    {
        return $this
            ->pheanstalk
            ->watch($this->getFullTubeName($tubeName))
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
        $payload = $this
            ->jobSerializer
            ->serialize($job)
        ;

        $jobId = $this
            ->pheanstalk
            ->putInTube(
                $this->getFullTubeName($tubeName),
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
        $fullTubeName = $this->getFullTubeName($tubeName);

        $pheanstalkJob = $this
            ->getTube($fullTubeName)
            ->reserveFromTube($fullTubeName, $timeout)
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
        $this
            ->getTube($this->getFullTubeName($tubeName))
            ->release(
                $this->getPheanstalkJob($job),
                $job->getPriority(),
                $job->getDelayOnRetry()
            )
        ;
    }

    public function bury($tubeName, JobInterface $job)
    {
        $this
            ->getTube($this->getFullTubeName($tubeName))
            ->bury($this->getPheanstalkJob($job))
        ;
    }

    public function delete($tubeName, JobInterface $job)
    {
        $this
            ->getTube($this->getFullTubeName($tubeName))
            ->delete($this->getPheanstalkJob($job))
        ;
    }

    public function countJobRetries(JobInterface $job)
    {
        $stats = $this
            ->pheanstalk
            ->statsJob($this->getPheanstalkJob($job))
        ;

        return isset($stats['releases']) ? $stats['releases'] : 0;
    }

    private function getTubeStats($tubeName, $property)
    {
        $fullTubeName = $this->getFullTubeName($tubeName);

        if (!in_array($fullTubeName, $this->pheanstalk->listTubes())) {
            return null;
        }

        $stats = $this
            ->pheanstalk
            ->statsTube($fullTubeName)
        ;

        return isset($stats[$property]) ? $stats[$property] : null;
    }

    public function countJobsBuried($tubeName)
    {
        return (int) $this->getTubeStats($tubeName, 'current-jobs-buried');
    }

    public function countJobsReady($tubeName)
    {
        return (int) $this->getTubeStats($tubeName, 'current-jobs-ready');
    }

    public function countJobsDelayed($tubeName)
    {
        return (int) $this->getTubeStats($tubeName, 'current-jobs-delayed');
    }

    public function countJobsReserved($tubeName)
    {
        return (int) $this->getTubeStats($tubeName, 'current-jobs-reserved');
    }

    public function countJobsWaiting($tubeName)
    {
        return (int) $this->getTubeStats($tubeName, 'current-jobs-waiting');
    }

    public function countJobsCompleted($tubeName)
    {
        $totalJobs = (int) $this->getTubeStats($tubeName, 'total-jobs');

        return
            $totalJobs
            - $this->countJobsReady($tubeName)
            - $this->countJobsReserved($tubeName)
            - $this->countJobsBuried($tubeName)
            - $this->countJobsDelayed($tubeName)
        ;
    }

    public function readNextJobReady($tubeName)
    {
        if ($this->countJobsReady($tubeName) == 0) {
            return;
        }

        $pheanstalkJob = $this
            ->pheanstalk
            ->peekReady($tubeName)
        ;

        $job = $this
            ->jobSerializer
            ->deserialize($pheanstalkJob->getData())
        ;

        $job->setId($pheanstalkJob->getId());

        return $job;
    }
}
