<?php

namespace Everlution\TubeBundle\Factory;

class JobFactory
{
    private $jobClass;

    public function __construct($jobClass)
    {
        $this->jobClass = $jobClass;
    }

    public function create()
    {
        $jobClass = $this->jobClass;

        return new $jobClass();
    }
}
