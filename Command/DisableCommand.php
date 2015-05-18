<?php

namespace Everlution\TubeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Everlution\TubeBundle\Command\Traits\SelectTubeTrait;
use Everlution\TubeBundle\Command\Interfaces\SelectTubeInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class DisableCommand extends ContainerAwareCommand implements SelectTubeInterface
{
    use SelectTubeTrait;

    protected function configure()
    {
        $this
            ->setName('everlution_tube:disable')
            ->setDescription('Disables the tube consumer')
            ->addArgument('tube', InputArgument::OPTIONAL, 'The tube ID')
            ->addOption('all', null, InputOption::VALUE_NONE, 'To disable all the enabled tubes')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tubes = array();
        if ($input->getOption('all')) {
            $tubes = $this->getTubes(self::ENABLED_TUBES);
        } else {
            $tubes[] = $this->selectTube($input, $output, self::ENABLED_TUBES);
        }

        foreach ($tubes as $tube) {
            if ($tube->isEnabled()) {
                $tube->disable();
                $output->writeln(sprintf('<comment>Tube <%s> disabled</comment>', $tube->getTubeName()));
            } else {
                $output->writeln(sprintf('<comment>Tube <%s> is already disabled</comment>', $tube->getTubeName()));
            }
        }

        $output->writeln('<info>WARNING:</info> the last jobs might still be in process.');
    }
}
