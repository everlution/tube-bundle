<?php

namespace Everlution\TubeBundle\Event;

abstract class JobEvents
{
    const PRODUCED = 'everlution_tube.event.job.produced';

    const RESERVED = 'everlution_tube.event.job.reserved';

    const CONSUMED = 'everlution_tube.event.job.consumed';

    const DELETED = 'everlution_tube.event.job.deleted';

    const FAILED = 'everlution_tube.event.job.failed';

    const RELEASED = 'everlution_tube.event.job.released';

    const BURIED = 'everlution_tube.event.job.buried';

    const INVALID = 'everlution_tube.event.job.invalid';

    const UNKNOWN_ERROR = 'everlution_tube.event.job.unknown_error';
}
