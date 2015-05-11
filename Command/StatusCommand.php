<?php

namespace Everlution\TubeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Everlution\TubeBundle\Command\Traits\SelectTubeTrait;
use Everlution\TubeBundle\Command\Interfaces\SelectTubeInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class StatusCommand extends ContainerAwareCommand implements SelectTubeInterface
{
    use SelectTubeTrait;

    protected function configure()
    {
        $this
            ->setName('everlution_tube:status')
            ->setDescription('Status for tubes')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tubes = $this->getTubes(self::ALL_TUBES);

        $table = new Table($output);
        $table->setHeaders(array(
            'Tube provider ID',
            'Tube name',
            'Status',
            'Reserved',
            'Ready',
            'Waiting',
            'Completed',
            'Delayed',
            'Buried',
        ));

        foreach ($tubes as $tubeId => $tube) {
            $table->addRow(array(
                $tubeId,
                $tube->getTubeName(),
                $tube->isEnabled() ? 'ENABLED' : 'DISABLED',
                sprintf('<comment>%s</comment>', $tube->countJobsReserved()),
                $tube->countJobsReady(),
                $tube->countJobsWaiting(),
                $tube->countJobsCompleted(),
                $tube->countJobsDelayed(),
                $tube->countJobsBuried(),
            ));
        }

        $table->render();
    }
}
