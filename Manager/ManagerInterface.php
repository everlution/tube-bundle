<?php

namespace Everlution\TubeBundle\Manager;

interface ManagerInterface
{
    public function isEnabled($tubeName);

    public function disable($tubeName);

    public function enable($tubeName);
}
