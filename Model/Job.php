<?php

namespace Everlution\TubeBundle\Model;

use Everlution\TubeBundle\Model\Interfaces\JobInterface;
use Everlution\TubeBundle\Model\Traits\JobFeaturesTrait;

class Job implements JobInterface
{
    use JobFeaturesTrait;

    private $id;

    private $payload;

    public function __construct()
    {
        $this->payload = array();
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setPayload(array $payload)
    {
        $this->payload = $payload;

        return $this;
    }

    public function getPayload()
    {
        return $this->payload;
    }
}
