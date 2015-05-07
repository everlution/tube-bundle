<?php

namespace Everlution\TubeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class RunCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('everlution_tube:run')
            ->setDescription('Run the tube consumer')
            ->addArgument('tube_provider', InputArgument::OPTIONAL, 'The service ID of the tube consumer')
            ->addOption(
                'ttr',
                null,
                InputOption::VALUE_OPTIONAL,
                'The minimum Time To Run for the script',
                60 * 60 // default 1 hour
            )
            ->addOption(
                'extra',
                null,
                InputOption::VALUE_OPTIONAL,
                'The max extra time for the script to run after the TTR expires',
                60 * 10 // default 10 minutes
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tubeServiceId = $input->getArgument('tube_provider');

        $tubeConsumer = $this
            ->getContainer()
            ->get($tubeServiceId)
        ;

        if ($tubeConsumer->isStopped()) {
            $tubeConsumer->start();
        }

        /*
         * We'll set our base time, which is one hour (in seconds).
         * Once we have our base time, we'll add anywhere between 0
         * to 10 minutes randomly, so all workers won't quick at the
         * same time.
         */
        $time_limit = $input->getOption('ttr');
        $time_limit += rand(0, $input->getOption('extra')); // Adding additional time

        // Set the start time
        $start_time = time();

        // Continue looping as long as we don't go past the time limit
        while (time() < $start_time + $time_limit) {
            if ($tubeConsumer->isStopped()) {
                break;
            }

            $tubeConsumer->consumeNext();

            gc_collect_cycles();
        }
    }
}
