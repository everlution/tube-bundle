<?php

namespace Everlution\TubeBundle\Serializer;

use JMS\Serializer\Serializer as JMSSerializer;
use Everlution\TubeBundle\Model\Interfaces\JobInterface;

class JMSJobSerializer implements JobSerializerInterface
{
    private $serializer;

    private $serializerFormat;

    private $jobClassName;

    public function __construct(
        JMSSerializer $serializer,
        $serializerFormat,
        $jobClassName
    ) {
        $this->serializer = $serializer;
        $this->serializerFormat = $serializerFormat;
        $this->jobClassName = $jobClassName;
    }

    public function serialize(JobInterface $job)
    {
        return $this
            ->serializer
            ->serialize($job, $this->serializerFormat)
        ;
    }

    public function deserialize($content)
    {
        return $this
            ->serializer
            ->deserialize($content, $this->jobClassName, $this->serializerFormat)
        ;
    }
}
