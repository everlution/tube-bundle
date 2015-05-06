<?php

namespace Everlution\TubeBundle\Exception;

class ServiceDownException extends \Exception
{
    public function __construct($host, $port, $code = null, $previous = null)
    {
        $message = sprintf(
            'Beanstalkd service is down %s:%s',
            $host,
            $port
        );

        parent::__construct($message, $code, $previous);
    }
}
