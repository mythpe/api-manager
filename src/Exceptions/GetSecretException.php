<?php

namespace Myth\Api\Exceptions;

use Exception;

/**
 * Class GetSecretException
 * @package Myth\Api\Exceptions
 */
class GetSecretException extends Exception
{

    /**
     * GetSecretException constructor.
     * @param string $message
     */
    public function __construct($message = 'Can not get application secret')
    {
        parent::__construct($message);
    }
}
