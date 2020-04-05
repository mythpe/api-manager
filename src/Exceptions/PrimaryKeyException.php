<?php

namespace Myth\Api\Exceptions;

use Exception;

/**
 * Class PrimaryKeyException
 * @package Myth\Api\Exceptions
 */
class PrimaryKeyException extends Exception
{

    /**
     * PrimaryKeyException constructor.
     * @param string $message
     */
    public function __construct($message = 'Model primary key required')
    {
        parent::__construct($message);
    }
}
