<?php

namespace Everlution\TubeBundle\Exception;

class InvalidJobException extends \Exception
{
    public function __construct($payload, $actual, $code, $previous)
    {
        $message = sprintf(
            'Invalid values for job %s',
            $payload
        );

        parent::__construct($message, $code, $previous);
    }
}
