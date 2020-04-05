<?php

namespace Myth\Api\Exceptions;

use Exception;

/**
 * Class ManagerRequestValidatorException
 * @package Myth\Api\Exceptions
 */
class ManagerRequestValidatorException extends Exception
{

    /**
     * ManagerRequestValidatorException constructor.
     * @param string $message
     */
    public function __construct($message = 'The given data was invalid.')
    {
        parent::__construct($message);
    }
}
