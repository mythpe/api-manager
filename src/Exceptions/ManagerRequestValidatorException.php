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
