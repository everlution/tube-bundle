<?php

namespace Everlution\TubeBundle\Command\Traits;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

trait SelectTubeProviderTrait
{
    private function getTubeProviders($status = null)
    {
        $tubeProviders = $this
            ->getContainer()
            ->get('everlution_tube.tube_provider_chain')
            ->getTubeProviders()
        ;

        $choices = array();
        foreach ($tubeProviders as $serviceId => $tubeProvider) {
            if ($status) {
                if ($status == 'PAUSED' && !$tubeProvider->isPaused()) {
                    continue;
                }
                if ($status == 'UNPAUSED' && $tubeProvider->isPaused()) {
                    continue;
                }
            }
            $choices[] = $serviceId;
        }

        return $choices;
    }

    private function selectTubeProvider(InputInterface $input, OutputInterface $output, $status = null)
    {
        $selectedTubeProvider = $input->getArgument('tube-provider');

        if (!$selectedTubeProvider) {
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                'Please select the tube provider',
                $this->getTubeProviders($status)
            );
            $selectedTubeProvider = $helper->ask($input, $output, $question);
        }

        if (!in_array($selectedTubeProvider, $this->getTubeProviders())) {
            throw new \Exception(
                sprintf('Invalid tube provider %s', $selectedTubeProvider)
            );
        }

        return $this
            ->getContainer()
            ->get($selectedTubeProvider)
        ;
    }
}
