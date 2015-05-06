<?php

namespace Everlution\TubeBundle\Serializer;

use Everlution\TubeBundle\Model\Interfaces\JobInterface;

interface JobSerializerInterface
{
    /**
     * serialize.
     *
     * @param JobInterface $job
     *
     * @return string
     */
    public function serialize(JobInterface $job);

    /**
     * deserialize.
     *
     * @param string $content
     *
     * @return \Everlution\TubeBundle\Job\JobInterface
     */
    public function deserialize($content);
}
