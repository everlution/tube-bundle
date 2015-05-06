<?php

namespace Everlution\TubeBundle\Job;

interface JobInterface
{
    public function setId($id);

    public function getId();

    public function setPriority($priority);

    public function getPriority();

    public function setDelay($delay);

    public function getDelay();

    public function setTtr($ttr);

    public function getTtr();
}
