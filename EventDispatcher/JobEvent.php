<?php

namespace Everlution\TubeBundle\EventDispatcher;

use Everlution\TubeBundle\Model\Interfaces\JobInterface;

class JobEvent extends \Symfony\Component\EventDispatcher\Event
{
    private $job;

    private $message;

    public function __construct(JobInterface $job, $message = '')
    {
        $this->job = $job;
        $this->message = $message;
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
