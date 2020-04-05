<?php

namespace Myth\Api\Exceptions;

use Exception;

/**
 * Class SecretNotSetupException
 * @package Myth\Api\Exceptions
 */
class SecretNotSetupException extends Exception
{

    /**
     * SecretNotSetupException constructor.
     * @param string $message
     */
    public function __construct($message = 'Error Secret')
    {
        parent::__construct($message);
    }
}
