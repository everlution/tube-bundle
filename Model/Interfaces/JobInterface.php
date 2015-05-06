<?php

namespace Everlution\TubeBundle\Model\Interfaces;

interface JobInterface extends JobFeaturesInterface
{
    public function setId($id);

    public function getId();

    public function setPayload(array $payload);

    public function getPayload();
}
