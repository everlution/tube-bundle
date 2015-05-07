<?php

namespace Everlution\TubeBundle\Adapter;

use Everlution\TubeBundle\Model\Interfaces\JobInterface;

interface AdapterInterface
{
    public function checkServiceUp();

    public function produce($tubeName, JobInterface $job);

    /**
     * reserve.
     *
     * @param string $tubeName
     *
     * @return JobInterface
     */
    public function reserve($tubeName);

    public function release($tubeName, JobInterface $job);

    public function bury($ubeName, JobInterface $job);

    public function delete($tubeName, JobInterface $job);

    public function countRetries(JobInterface $job);
}
