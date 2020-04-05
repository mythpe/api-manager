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
