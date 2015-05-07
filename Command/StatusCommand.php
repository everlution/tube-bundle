<?php

namespace Everlution\TubeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Everlution\TubeBundle\Command\Traits\SelectTubeProviderTrait;
use Everlution\TubeBundle\Command\Interfaces\SelectTubeProviderInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class StatusCommand extends ContainerAwareCommand implements SelectTubeProviderInterface
{
    use SelectTubeProviderTrait;

    protected function configure()
    {
        $this
            ->setName('everlution_tube:status')
            ->setDescription('Status for tubes')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tubeProvidersIds = $this->getTubeProvidersIds(self::ALL_TUBES);

        $table = new Table($output);
        $table->setHeaders(array(
            'Tube provider ID',
            'Tube name',
            'Status',
            'Reserved',
            'Ready',
            'Waiting',
            'Delayed',
            'Buried',
        ));

        foreach ($tubeProvidersIds as $tubeProviderId) {
            $tubeProvider = $this
                ->getContainer()
                ->get($tubeProviderId)
            ;
            $table->addRow(array(
                $tubeProviderId,
                $tubeProvider->getTubeName(),
                $tubeProvider->isEnabled() ? 'ENABLED' : 'DISABLED',
                $tubeProvider->countJobsReserved(),
                $tubeProvider->countJobsReady(),
                $tubeProvider->countJobsWaiting(),
                $tubeProvider->countJobsDelayed(),
                $tubeProvider->countJobsBuried(),
            ));
        }

        $table->render();
    }
}
