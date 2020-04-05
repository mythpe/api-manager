<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Mixins;

use Myth\Api\Facades\Api;

/**
 * Class RequestMixin
 * @package Myth\Api\Mixins
 */
class RequestMixin
{

    /** @var string */
    protected $authManager = null;

    /**
     * @return \Closure
     */
    public function authManager()
    {
        return function () {
            if(!is_null($this->authManager)){
                return $this->authManager;
            }
            return null;
        };
    }

    /**
     * @return \Closure
     */
    public function setAuthManager()
    {
        return function ($name) {
            $this->authManager = Api::manager($name);
        };
    }
}