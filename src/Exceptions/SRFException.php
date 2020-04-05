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
 * Class SRFException
 * @package Myth\Api\Exceptions
 */
class SRFException extends Exception
{

    /**
     * SRFException constructor.
     * @param string $message
     */
    public function __construct($message = 'Error SRF Token')
    {
        parent::__construct($message);
    }
}
