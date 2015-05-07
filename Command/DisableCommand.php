<?php

namespace Everlution\TubeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Everlution\TubeBundle\Command\Traits\SelectTubeProviderTrait;
use Everlution\TubeBundle\Command\Interfaces\SelectTubeProviderInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class DisableCommand extends ContainerAwareCommand implements SelectTubeProviderInterface
{
    use SelectTubeProviderTrait;

    protected function configure()
    {
        $this
            ->setName('everlution_tube:disable')
            ->setDescription('Disables the tube consumer')
            ->addArgument('tube-provider', InputArgument::OPTIONAL, 'The tube provider ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tubeProvider = $this->selectTubeProvider($input, $output, self::ENABLED_TUBES);

        if ($tubeProvider->isEnabled()) {
            $tubeProvider->disable();
            $output->writeln('<comment>Tube provider disabled</comment>');
            $output->writeln('<info>WARNING:</info> the last job might still be in process.');
        } else {
            $output->writeln('<comment>The tube provider is already disabled</comment>');
        }
    }
}
