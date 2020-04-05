<?php

namespace Myth\Api\Exceptions;

use Exception;

/**
 * Class MakeSecretException
 * @package Myth\Api\Exceptions
 */
class MakeSecretException extends Exception
{

    /**
     * MakeSecretException constructor.
     * @param string $message
     */
    public function __construct($message = 'Can not make a new secret')
    {
        parent::__construct($message);
    }
}
