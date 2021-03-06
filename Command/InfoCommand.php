<?php

namespace Everlution\TubeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Everlution\TubeBundle\Command\Traits\SelectTubeTrait;
use Everlution\TubeBundle\Command\Interfaces\SelectTubeInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;

class InfoCommand extends ContainerAwareCommand implements SelectTubeInterface
{
    use SelectTubeTrait;

    protected function configure()
    {
        $this
            ->setName('everlution_tube:info')
            ->setDescription('Info on the tubes')
            ->addArgument('tube', InputArgument::OPTIONAL, 'The tube ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $tubeProvider \Everlution\TubeBundle\Tube\TubeInterface */
        $tubeProvider = $this->selectTube($input, $output, self::ALL_TUBES);

        $table = new Table($output);
        $table
            ->setHeaders(array('Attribute', 'Value'))
            ->setRows(array(
                array('Tube name', $tubeProvider->getTubeName()),
                array('Status', $tubeProvider->isEnabled() ? 'ENABLED' : 'DISABLED'),
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
