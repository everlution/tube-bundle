<?php

namespace Everlution\TubeBundle\Event;

abstract class TubeEvents
{
    const SERVICE_DOWN = 'everlution.tube.event.tube.service_down';

    const PAUSED = 'everlution.tube.event.tube.paused';

    const UNPAUSED = 'everlution.tube.event.tube.unpaused';
}
