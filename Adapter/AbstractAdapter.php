<?php

namespace Everlution\TubeBundle\Adapter;

use Everlution\TubeBundle\Serializer\JobSerializerInterface;

abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * @var JobSerializerInterface
     */
    protected $jobSerializer;

    protected $prefix;

    public function __construct(JobSerializerInterface $jobSerializer, $prefix = '')
    {
        $this->jobSerializer = $jobSerializer;
        $this->prefix = $prefix;
    }

    protected function getFullTubeName($tubeName)
    {
        return sprintf('%s%s', $this->prefix, $tubeName);
    }
}
