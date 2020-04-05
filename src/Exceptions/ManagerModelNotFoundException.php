<?php

namespace Myth\Api\Exceptions;

use Exception;

/**
 * Class ManagerModelNotFoundException
 * @package Myth\Api\Exceptions
 */
class ManagerModelNotFoundException extends Exception
{

    /**
     * ManagerModelNotFoundException constructor.
     * @param string $message
     */
    public function __construct($message = 'Model not found')
    {
        parent::__construct($message);
    }
}
