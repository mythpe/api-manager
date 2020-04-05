<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

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
