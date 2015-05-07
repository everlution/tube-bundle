<?php

namespace Everlution\TubeBundle\Adapter;

use Everlution\TubeBundle\Serializer\JobSerializerInterface;

abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * @var JobSerializerInterface
     */
    protected $jobSerializer;

    public function __construct(JobSerializerInterface $jobSerializer)
    {
        $this->jobSerializer = $jobSerializer;
    }
}
