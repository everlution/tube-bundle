<?php

namespace Everlution\TubeBundle\Serializer;

use Everlution\TubeBundle\Model\Interfaces\JobInterface;

class DefaultJsonJobSerializer implements JobSerializerInterface
{
    /**
     * @var \Everlution\TubeBundle\Factory\JobFactory
     */
    private $jobFactory;

    public function __construct($jobFactory)
    {
        $this->jobFactory = $jobFactory;
    }

    /**
     * serialize.
     *
     * @param JobInterface $job
     *
     * @return string
     */
    public function serialize(JobInterface $job)
    {
        $array = array(
            'id' => $job->getId(),
            'priority' => $job->getPriority(),
            'delay' => $job->getDelay(),
            'ttr' => $job->getTtr(),
            'maxRetriesOnFailure' => $job->getMaxRetriesOnFailure(),
            'delayOnRetry' => $job->getDelayOnRetry(),
            'payload' => $job->getPayload(),
        );

        return json_encode($array);
    }

    /**
     * deserialize.
     *
     * @param string $content
     *
     * @return JobInterface
     */
    public function deserialize($content)
    {
        $jobJson = json_decode($content, true);

        return $this
            ->jobFactory
            ->create()
            ->setId($jobJson['id'])
            ->setPriority($jobJson['priority'])
            ->setDelay($jobJson['delay'])
            ->setTtr($jobJson['ttr'])
            ->setMaxRetriesOnFailure($jobJson['maxRetriesOnFailure'])
            ->setDelayOnRetry($jobJson['delayOnRetry'])
            ->setPayload($jobJson['payload'])
        ;
    }
}
