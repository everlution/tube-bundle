<?php

namespace Everlution\TubeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Everlution\TubeBundle\Command\Traits\SelectTubeProviderTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class StopCommand extends ContainerAwareCommand
{
    use SelectTubeProviderTrait;

    protected function configure()
    {
        $this
            ->setName('everlution_tube:stop')
            ->setDescription('Stops the tube consumer')
            ->addArgument('tube-provider', InputArgument::OPTIONAL, 'The tube provider ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tubeProvider = $this->selectTubeProvider($input, $output);

        if ($tubeProvider->isRunning()) {
            $tubeProvider->stop();
        }

        $output->writeln('<comment>Stopping tube provider</comment>');

        $output->writeln('<info>WARNING:</info> the last job might still be in process.');
    }
}
