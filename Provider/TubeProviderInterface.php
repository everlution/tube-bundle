<?php

namespace Everlution\TubeBundle\Provider;

use Everlution\TubeBundle\Model\Interfaces\JobFeaturesInterface;
use Everlution\TubeBundle\Model\Interfaces\JobInterface;

interface TubeProviderInterface extends JobFeaturesInterface
{
    public function produce(JobInterface $job);

    public function consumeNext();

    public function isRunning();

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
    public function isStopped();

    /**
     * stop.
     *
     * Stops the produce/consume actions persisting the status somewhere.
     */
    public function stop();

    /**
     * start.
     *
     * Starts the produce/consume actions persisting the status somewhere.
     */
    public function start();

    /**
     * validateJob.
     *
     * This method is in charge of validating the payload of the job.
     *
     * @return bool
     */
    public function validateJob(JobInterface $job);

    /**
     * consumeOne.
     *
     * This method contains the logic on how to consume a job in this tube.
     * It's called in a loop by consumeNext().
     *
     * No need to return any value, so please throw exceptions if any error
     * happens.
     */
    public function consumeOne(JobInterface $job);
}
