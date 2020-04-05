<?php

namespace Myth\Api\Exceptions;

use Exception;

/**
 * Class HeaderManagerKeyException
 * @package Myth\Api\Exceptions
 */
class HeaderManagerKeyException extends Exception
{

    /**
     * HeaderManagerKeyException constructor.
     * @param string $message
     */
    public function __construct($message = 'Manager name not provided')
    {
        parent::__construct($message);
    }
}
