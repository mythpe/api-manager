<?php

namespace Myth\Api\Exceptions;

use Exception;

/**
 * Class ClientNotFoundException
 * @package Myth\Api\Exceptions
 */
class ClientNotFoundException extends Exception
{

    /**
     * ClientNotFoundException constructor.
     * @param string $message
     */
    public function __construct($message = 'Client not found')
    {
        parent::__construct($message);
    }
}
