<?php

namespace Myth\Api\Exceptions;

use Exception;

/**
 * Class ManagerNotFoundException
 * @package Myth\Api\Exceptions
 */
class ManagerNotFoundException extends Exception
{

    /**
     * ManagerNotFoundException constructor.
     * @param string $message
     */
    public function __construct($message = 'Manager not found')
    {
        parent::__construct($message);
    }
}
