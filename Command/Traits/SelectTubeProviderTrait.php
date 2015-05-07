<?php

namespace Everlution\TubeBundle\Command\Traits;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

trait SelectTubeProviderTrait
{
    public function getTubeProvidersIds($status = self::ALL_TUBES)
    {
        $tubeProviders = $this
            ->getContainer()
            ->get('everlution_tube.tube_provider_chain')
            ->getTubeProviders()
        ;

        $choices = array();
        foreach ($tubeProviders as $serviceId => $tubeProvider) {
            if (($status == self::DISABLED_TUBES && !$tubeProvider->isEnabled())
                || ($status == self::ENABLED_TUBES && $tubeProvider->isEnabled())
                || $status == self::ALL_TUBES
            ) {
                $choices[] = $serviceId;
            }
        }

        return $choices;
    }

    public function selectTubeProvider(InputInterface $input, OutputInterface $output, $status = self::ALL_TUBES)
    {
        $selectedTubeProvider = $input->getArgument('tube-provider');

        $tubeProvidersIds = $this->getTubeProvidersIds($status);

        if (!$selectedTubeProvider) {
            if (count($tubeProvidersIds) == 0) {
                throw new \Exception('No tube providers found');
            }
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                $this->getTubeProviderChoiceMessage($status),
                $tubeProvidersIds
            );
            $selectedTubeProvider = $helper->ask($input, $output, $question);
        }

        if (!in_array($selectedTubeProvider, $tubeProvidersIds)) {
            throw new \Exception(
                sprintf('Invalid tube provider %s', $selectedTubeProvider)
            );
        }

        return $this
            ->getContainer()
            ->get($selectedTubeProvider)
        ;
    }

    private function getTubeProviderChoiceMessage($status)
    {
        $message = 'Select the tube provider ';

        switch ($status) {
            case self::ENABLED_TUBES:
                $message .= '(this list contains ONLY ENABLED providers)';
                break;
            case self::DISABLED_TUBES:
                $message .= '(this list contains ONLY DISABLED providers)';
                break;
            default:
                $message .= '(this list contains ALL the providers)';
        }

        return $message;
    }
}
