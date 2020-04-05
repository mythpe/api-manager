<?php

namespace Myth\Api\Exceptions;

use Exception;

/**
 * Class SRFException
 * @package Myth\Api\Exceptions
 */
class SRFException extends Exception
{

    /**
     * SRFException constructor.
     * @param string $message
     */
    public function __construct($message = 'Error SRF Token')
    {
        parent::__construct($message);
    }
}
