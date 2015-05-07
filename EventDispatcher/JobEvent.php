<?php

namespace Everlution\TubeBundle\EventDispatcher;

use Everlution\TubeBundle\Model\Interfaces\JobInterface;

class JobEvent extends \Symfony\Component\EventDispatcher\Event
{
    private $tubeName;

    private $job;

    private $message;

    public function __construct($tubeName, JobInterface $job, $message = '')
    {
        $this->tubeName = $tubeName;
        $this->job = $job;
        $this->message = $message;
    }

    public function getTubeName()
    {
        return $this->tubeName;
    }

    public function getJob()
    {
        return $this->job;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
