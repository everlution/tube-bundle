<?php

namespace Everlution\TubeBundle;

use Everlution\TubeBundle\Tube\TubeInterface;

class TubeChain
{
    private $tubes;

    public function __construct()
    {
        $this->tubes = array();
    }

    public function addTube($serviceId, TubeInterface $tube)
    {
        $this->tubes[$serviceId] = $tube;

        return $this;
    }

    public function getTubes()
    {
        return $this->tubes;
    }
}
