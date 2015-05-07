<?php

namespace Everlution\TubeBundle\Adapter;

use Everlution\TubeBundle\Model\Interfaces\JobInterface;

interface AdapterInterface
{
    /**
     * checkServiceUp.
     *
     * If the service is not reachable it will throw an exception.
     */
    public function checkServiceUp();

    /**
     * produce.
     *
     * Enqueues a new job.
     *
     * @param string       $tubeName
     * @param JobInterface $job
     */
    public function produce($tubeName, JobInterface $job);

    /**
     * reserve.
     *
     * Returns the next job ready to be consumed.
     *
     * @param string $tubeName
     *
     * @return JobInterface
     */
    public function reserve($tubeName);

    /**
     * release.
     *
     * Puts the job back in the queue.
     *
     * @param string       $tubeName
     * @param JobInterface $job
     */
    public function release($tubeName, JobInterface $job);

    /**
     * bury.
     *
     * Discards a job in a specific.
     *
     * @param string       $tubeName
     * @param JobInterface $job
     */
    public function bury($tubeName, JobInterface $job);

    /**
     * delete.
     *
     * Deletes a job from the queue.
     *
     * @param string       $tubeName
     * @param JobInterface $job
     */
    public function delete($tubeName, JobInterface $job);

    /**
     * countJobRetries.
     *
     * Counts the number of times the system has tried to consume the job
     * and enqueued it back in the tube.
     *
     * @param JobInterface $job
     */
    public function countJobRetries(JobInterface $job);

    public function countJobsReady($tubeName);

    public function countJobsBuried($tubeName);

    public function countJobsDelayed($tubeName);

    public function countJobsReserved($tubeName);

    public function countWaitingJobs($tubeName);

    public function readNextJobReady($tubeName);
}
