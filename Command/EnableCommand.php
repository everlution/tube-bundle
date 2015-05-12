<?php

namespace Everlution\TubeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Everlution\TubeBundle\Command\Traits\SelectTubeTrait;
use Everlution\TubeBundle\Command\Interfaces\SelectTubeInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class EnableCommand extends ContainerAwareCommand implements SelectTubeInterface
{
    use SelectTubeTrait;

    protected function configure()
    {
        $this
            ->setName('everlution_tube:enable')
            ->setDescription('Enables the tube consumer')
            ->addArgument('tube', InputArgument::OPTIONAL, 'The tube ID')
            ->addOption('all', null, InputOption::VALUE_NONE, 'To enable all the disabled tubes')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tubes = array();
        if ($input->hasOption('all')) {
            $tubes = $this->getTubes(self::DISABLED_TUBES);
        } else {
            $tubes[] = $this->selectTube($input, $output, self::DISABLED_TUBES);
        }

        foreach ($tubes as $tube) {
            if (!$tube->isEnabled()) {
                $tube->enable();
                $output->writeln(sprintf('<comment>Tube <%s> enabled</comment>', $tube->getTubeName()));
            } else {
                $output->writeln(sprintf('<comment>Tube <%s> is already enabled</comment>', $tube->getTubeName()));
            }
        }
    }
}
