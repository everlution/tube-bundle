<?php

namespace Everlution\TubeBundle\Event;

abstract class TubeEvents
{
    const SERVICE_DOWN = 'everlution.tube.event.service_down';

    const JOB_PRODUCED = 'everlution.tube.event.job.produced';

    const JOB_CONSUMED = 'everlution.tube.event.job.consumed';

    const JOB_FAILED = 'everlution.tube.event.job.failed';
}
