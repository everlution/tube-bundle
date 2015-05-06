<?php

namespace Everlution\TubeBundle\Exception\Job;

class InvalidTypeException extends \Exception
{
    public function __construct($expected, $actual, $code, $previous)
    {
        $message = sprintf(
            'Expecting class %s. %s provided.',
            $expected,
            $actual
        );

        parent::__construct($message, $code, $previous);
    }
}
