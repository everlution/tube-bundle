<?php

namespace Everlution\TubeBundle\Job;

use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;

class FirstJob extends AbstractJob implements JobInterface
{
    /**
     * @Expose
     * @Type("string")
     */
    private $field1;

    /**
     * @Expose
     * @Type("string")
     */
    private $field2;

    public function setField1($field1)
    {
        $this->field1 = $field1;

        return $this;
    }

    public function setField2($field2)
    {
        $this->field2 = $field2;

        return $this;
    }
}
