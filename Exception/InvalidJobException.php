<?php

namespace Everlution\TubeBundle\Exception;

class InvalidJobException extends \Exception
{
    public function __construct($message, $code, $previous)
    {
        $message = sprintf('Invalid job: %s', $message);

        parent::__construct($message, $code, $previous);
    }
}
