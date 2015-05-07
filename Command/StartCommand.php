<?php

namespace Everlution\TubeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Everlution\TubeBundle\Command\Traits\SelectTubeProviderTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class StartCommand extends ContainerAwareCommand
{
    use SelectTubeProviderTrait;

    protected function configure()
    {
        $this
            ->setName('everlution_tube:start')
            ->setDescription('Starts the tube consumer')
            ->addArgument('tube-provider', InputArgument::OPTIONAL, 'The tube provider ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tubeProvider = $this->selectTubeProvider($input, $output);

        if ($tubeProvider->isStopped()) {
            $tubeProvider->start();
        }

        $output->writeln('<comment>Tube provider started</comment>');
    }
}
