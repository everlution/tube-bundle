<?php

namespace Everlution\TubeBundle\Provider;

use Everlution\TubeBundle\Model\Interfaces\JobFeaturesInterface;
use Everlution\TubeBundle\Model\Interfaces\JobInterface;

interface TubeProviderInterface extends JobFeaturesInterface
{
    public function getTubeName();

    public function checkServiceUp();

    public function produce(JobInterface $job);

    public function consumeNext();

    /**
     * isEnabled.
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * disable.
     *
     * Stops the produce/consume actions persisting the status somewhere.
     */
    public function disable();

    /**
     * enable.
     *
     * Starts the produce/consume actions persisting the status somewhere.
     */
    public function enable();

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

    public function countJobsReady();

    public function countJobsBuried();

    public function countJobsDelayed();

    public function countJobsReserved();

    public function countJobsWaiting();

    public function readNextJobReady();
}
