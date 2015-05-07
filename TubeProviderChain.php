<?php

namespace Everlution\TubeBundle;

use Everlution\TubeBundle\Provider\TubeProviderInterface;

class TubeProviderChain
{
    private $tubeProviders;

    public function __construct()
    {
        $this->tubeProviders = array();
    }

    public function addTubeProvider($serviceId, TubeProviderInterface $tubeProvider)
    {
        $this->tubeProviders[$serviceId] = $tubeProvider;

        return $this;
    }

    public function getTubeProviders()
    {
        return $this->tubeProviders;
    }
}
