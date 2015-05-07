<?php

namespace Everlution\TubeBundle\Runner;

interface RunnerInterface
{
    public function isPaused($tubeName);

    public function pause($tubeName);

    public function unpause($tubeName);
}
