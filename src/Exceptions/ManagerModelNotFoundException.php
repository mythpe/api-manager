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
