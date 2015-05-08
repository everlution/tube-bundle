<?php

namespace Everlution\TubeBundle\EventDispatcher;

class TubeEvent extends \Symfony\Component\EventDispatcher\Event
{
    private $tubeName;

    private $message;

    public function __construct($tubeName, $message = '')
    {
        $this->tubeName = $tubeName;
        $this->message = $message;
    }

    public function getTubeName()
    {
        return $this->tubeName;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
