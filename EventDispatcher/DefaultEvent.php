<?php

namespace Everlution\TubeBundle\EventDispatcher;

class DefaultEvent extends \Symfony\Component\EventDispatcher\Event
{
    private $data;

    public function __construct(array $data = array())
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
