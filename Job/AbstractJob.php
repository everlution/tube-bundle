<?php

namespace Everlution\TubeBundle\Job;

use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;

abstract class AbstractJob implements JobInterface
{
    private $id;

    /**
     * @Expose
     * @Type("integer")
     */
    private $priority;

    /**
     * @Expose
     * @Type("integer")
     */
    private $delay;

    /**
     * @Expose
     * @Type("integer")
     */
    private $ttr;

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setDelay($delay)
    {
        $this->delay = $delay;

        return $this;
    }

    public function getDelay()
    {
        return $this->delay;
    }

    public function setTtr($ttr)
    {
        $this->ttr = $ttr;

        return $this;
    }

    public function getTtr()
    {
        return $this->ttr;
    }
}
