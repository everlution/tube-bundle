<?php

namespace Everlution\TubeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Everlution\TubeBundle\Command\Traits\SelectTubeProviderTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

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
        $tubeProvider = $this->selectTubeProvider($input, $output);
    }
}
