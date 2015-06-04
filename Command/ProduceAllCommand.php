<?php

namespace Everlution\TubeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Everlution\TubeBundle\Command\Traits\SelectTubeTrait;
use Everlution\TubeBundle\Command\Interfaces\SelectTubeInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ProduceAllCommand extends ContainerAwareCommand implements SelectTubeInterface
{
    use SelectTubeTrait;

    protected function configure()
    {
        $this
            ->setName('everlution_tube:produce_all')
            ->setDescription('Produces all the jobs for the tubes')
            ->addArgument('tube', InputArgument::OPTIONAL, 'The tube ID')
            ->addOption('all', null, InputOption::VALUE_NONE, 'To produce all the the jobs for every tube')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tubes = array();
        if ($input->getOption('all')) {
            $tubes = $this->getTubes(self::ALL_TUBES);
        } else {
            $tubes[] = $this->selectTube($input, $output, self::ALL_TUBES);
        }

        foreach ($tubes as $tube) {
            $output->writeln(sprintf('<comment>Producing all jobs for tube <%s></comment>', $tube->getTubeName()));
            $tube->produceAll();
        }
    }
}
