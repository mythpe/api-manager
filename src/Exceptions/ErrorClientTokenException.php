<?php

namespace Myth\Api\Exceptions;

use Exception;

/**
 * Class ErrorClientTokenException
 * @package Myth\Api\Exceptions
 */
class ErrorClientTokenException extends Exception
{

    /**
     * ErrorClientTokenException constructor.
     * @param string $message
     */
    public function __construct($message = 'Error Client Secret')
    {
        parent::__construct($message);
    }
}
