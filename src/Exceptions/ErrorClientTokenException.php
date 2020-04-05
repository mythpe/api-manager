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
 * Class ErrorClientTokenException
 * @package Myth\Api\Exceptions
 */
class ErrorClientTokenException extends Exception
{

    /**
     * ErrorClientTokenException constructor.
     * @param string $message
     */
    public function __construct($message = 'Error Client Secret')
    {
        parent::__construct($message);
    }
}
