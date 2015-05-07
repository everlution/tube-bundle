<?php

namespace Everlution\TubeBundle\EventDispatcher;

class DefaultEvent extends \Symfony\Component\EventDispatcher\Event
{
    private $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
