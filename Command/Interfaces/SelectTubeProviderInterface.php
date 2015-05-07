<?php

namespace Everlution\TubeBundle\Command\Interfaces;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface SelectTubeProviderInterface
{
    const ALL_TUBES = 'all';

    const ENABLED_TUBES = 'enabled';

    const DISABLED_TUBES = 'disabled';

    public function getTubeProvidersIds($status = self::ALL_TUBES);

    public function selectTubeProvider(InputInterface $input, OutputInterface $output, $status = self::ALL_TUBES);
}
