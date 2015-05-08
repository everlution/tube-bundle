<?php

namespace Everlution\TubeBundle\Exception;

class JobConsumeException extends \Exception
{
    public function __construct($message, $code, $previous)
    {
        $message = sprintf('Error consuming job: %s', $message);

        parent::__construct($message, $code, $previous);
    }
}
