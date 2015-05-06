<?php

namespace Everlution\TubeBundle\Model\Traits;

use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;

trait JobFeaturesTrait
{
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

    /**
     * @Expose
     * @Type("integer")
     */
    private $maxRetriesOnFailure;

    /**
     * @Expose
     * @Type("integer")
     */
    private $delayOnRetry;

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

    public function setMaxRetriesOnFailure($maxRetriesOnFailure)
    {
        $this->maxRetriesOnFailure = $maxRetriesOnFailure;

        return $this;
    }

    public function getMaxRetriesOnFailure()
    {
        return $this->maxRetriesOnFailure;
    }

    public function setDelayOnRetry($delayOnRetry)
    {
        $this->delayOnRetry = $delayOnRetry;

        return $this;
    }

    public function getDelayOnRetry()
    {
        return $this->delayOnRetry;
    }
}
