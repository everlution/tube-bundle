<?php

namespace Everlution\TubeBundle\Command\Traits;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

trait SelectTubeTrait
{
    public function getTubes($status = self::ALL_TUBES)
    {
        $tubes = $this
            ->getContainer()
            ->get('everlution_tube.tube_chain')
            ->getTubes()
        ;

        $choices = array();
        foreach ($tubes as $serviceId => $tube) {
            if (($status == self::DISABLED_TUBES && !$tube->isEnabled())
                || ($status == self::ENABLED_TUBES && $tube->isEnabled())
                || $status == self::ALL_TUBES
            ) {
                $choices[$serviceId] = $tube;
            }
        }

        return $choices;
    }

    public function selectTube(InputInterface $input, OutputInterface $output, $status = self::ALL_TUBES)
    {
        $selectedTube = $input->getArgument('tube');

        $tubes = $this->getTubes($status);

        if (!$selectedTube) {
            if (count($tubes) == 0) {
                throw new \Exception('No available tubes found');
            }
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                $this->getTubeChoiceMessage($status),
                array_keys($tubes)
            );
            $selectedTube = $helper->ask($input, $output, $question);
        }

        if (!in_array($selectedTube, array_keys($tubes))) {
            throw new \Exception(
                sprintf('Invalid tube %s', $selectedTube)
            );
        }

        return $this
            ->getContainer()
            ->get($selectedTube)
        ;
    }

    private function getTubeChoiceMessage($status)
    {
        $message = 'Select the tube ';

        switch ($status) {
            case self::ENABLED_TUBES:
                $message .= '<info>(this list contains ONLY ENABLED providers)</info>';
                break;
            case self::DISABLED_TUBES:
                $message .= '<info>(this list contains ONLY DISABLED providers)</info>';
                break;
            default:
                $message .= '<info>(this list contains ALL the providers)</info>';
        }

        return $message;
    }
}
