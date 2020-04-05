<?php

namespace Myth\Api\Exceptions;

use Exception;

/**
 * Class ManagerNotProvidedException
 * @package Myth\Api\Exceptions
 */
class ManagerNotProvidedException extends Exception
{

    /**
     * ManagerNotProvidedException constructor.
     * @param string $message
     */
    public function __construct($message = 'Manager not provided')
    {
        parent::__construct($message);
    }
}
