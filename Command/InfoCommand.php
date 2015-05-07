<?php

namespace Everlution\TubeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Everlution\TubeBundle\Command\Traits\SelectTubeProviderTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;

class InfoCommand extends ContainerAwareCommand
{
    use SelectTubeProviderTrait;

    protected function configure()
    {
        $this
            ->setName('everlution_tube:info')
            ->setDescription('Info on the tubes')
            ->addArgument('tube-provider', InputArgument::OPTIONAL, 'The tube name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $tubeProvider \Everlution\TubeBundle\Provider\TubeProviderInterface */
        $tubeProvider = $this->selectTubeProvider($input, $output);

        $table = new Table($output);
        $table
            ->setHeaders(array('Attribute', 'Value'))
            ->setRows(array(
                array('Tube name', $tubeProvider->getTubeName()),
                array('Status', $tubeProvider->isStopped() ? 'STOPPED' : 'RUNNING'),
                new TableSeparator(),
                array('Default priority', $tubeProvider->getPriority()),
                array('Default max retries on failure', $tubeProvider->getMaxRetriesOnFailure()),
                array('Default TTR', $tubeProvider->getTtr()),
                array('Default delay', $tubeProvider->getDelay()),
                array('Default delay on retry', $tubeProvider->getDelayOnRetry()),
                new TableSeparator(),
                array('Reserved jobs', $tubeProvider->countJobsReserved()),
                array('Ready jobs', $tubeProvider->countJobsReady()),
                array('Waiting jobs', $tubeProvider->countJobsWaiting()),
                array('Delayed jobs', $tubeProvider->countJobsDelayed()),
                array('Buried jobs', $tubeProvider->countJobsBuried()),
            ))
        ;

        $nextJobReady = $tubeProvider->readNextJobReady();
        if ($nextJobReady) {
            $table->addRow(array(
                'Next job ready',
                json_encode($nextJobReady->getPayload()),
            ));
        }

        $table->render();
    }
}
