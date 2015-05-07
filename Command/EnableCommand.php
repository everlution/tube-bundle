<?php

namespace Everlution\TubeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Everlution\TubeBundle\Command\Traits\SelectTubeProviderTrait;
use Everlution\TubeBundle\Command\Interfaces\SelectTubeProviderInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class EnableCommand extends ContainerAwareCommand implements SelectTubeProviderInterface
{
    use SelectTubeProviderTrait;

    protected function configure()
    {
        $this
            ->setName('everlution_tube:enable')
            ->setDescription('Enables the tube consumer')
            ->addArgument('tube-provider', InputArgument::OPTIONAL, 'The tube provider ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tubeProvider = $this->selectTubeProvider($input, $output, self::DISABLED_TUBES);

        if (!$tubeProvider->isEnabled()) {
            $tubeProvider->enable();
            $output->writeln('<comment>Tube provider enabled</comment>');
        } else {
            $output->writeln('<comment>The tube provider is already enabled</comment>');
        }
    }
}
