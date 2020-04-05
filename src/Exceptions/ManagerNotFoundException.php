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
