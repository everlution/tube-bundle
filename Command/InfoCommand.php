<?php

namespace Everlution\TubeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Helper\Table;

class InfoCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('everlution_tube:info')
            ->setDescription('Info on the tubes')
            ->addArgument('tube', InputArgument::OPTIONAL, 'The tube name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $pheanstalk \Pheanstalk\Pheanstalk */
        $pheanstalk = $this
            ->getContainer()
            ->get('everlution_tube.pheanstalk')
        ;

        // selecting the tube
        $selectedTube = $input->getArgument('tube');

        if (!$selectedTube) {
            $tubes = array();
            foreach ($pheanstalk->listTubes() as $tube) {
                $tubes[] = $tube;
            }

            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                'Please select the tube',
                $tubes
            );
            $selectedTube = $helper->ask($input, $output, $question);
        }

        $tubeStats = $pheanstalk->statsTube($selectedTube);

        $table = new Table($output);
        foreach ($tubeStats as $key => $value) {
            $table->addRow(array(
                $key,
                $value,
            ));
        }
        $table->render();

        if ($tubeStats['current-jobs-ready']) {
            $output->writeln(sprintf('<info>Next Job Ready</info>'));
            $nextJobReady = $pheanstalk->peekReady($selectedTube);
            $output->write(sprintf('<comment>%s</comment>', $nextJobReady->getData()));
        }

        $output->writeln('');
    }
}
