<?php

namespace Everlution\TubeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class StopCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('everlution_tube:stop')
            ->setDescription('Stops the tube consumer')
            ->addArgument('tube_provider', InputArgument::OPTIONAL, 'The service ID of the tube consumer')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tubeServiceId = $input->getArgument('tube_provider');

        $tubeConsumer = $this
            ->getContainer()
            ->get($tubeServiceId)
        ;

        if ($tubeConsumer->isRunning()) {
            $tubeConsumer->stop();
        }

        $output->write(
            sprintf('<comment>Stopping tube provider %s</comment>', $tubeServiceId)
        );

        $output->write(
            sprintf('<info>WARNING:</info> the last job might still be in process.', $tubeServiceId)
        );
    }
}
