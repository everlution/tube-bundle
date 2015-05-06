<?php

namespace Everlution\TubeBundle\Model\Interfaces;

interface JobFeaturesInterface
{
    public function setPriority($priority);

    public function getPriority();

    public function setDelay($delay);

    public function getDelay();

    public function setTtr($ttr);

    public function getTtr();

    public function setMaxRetriesOnFailure($maxRetriesOnFailure);

    public function getMaxRetriesOnFailure();

    public function setDelayOnRetry($delayOnRetry);

    public function getDelayOnRetry();
}
