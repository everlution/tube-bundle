<?php

namespace Everlution\TubeBundle\Event;

abstract class JobEvents
{
    const PRODUCED = 'everlution_tube.event.job.produced';

    const CONSUMED = 'everlution_tube.event.job.consumed';

    const FAILED = 'everlution_tube.event.job.failed';

    const DISCARDED = 'everlution_tube.event.job.discarded';
}
