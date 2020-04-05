<?php

namespace Myth\Api\Mixins;

use Myth\Api\Facades\Api;

class RequestMixin
{

    /** @var string */
    protected $authManager = null;

    public function authManager()
    {
        return function () {
            if(!is_null($this->authManager)){
                return $this->authManager;
            }
            return null;
        };
    }

    public function setAuthManager()
    {
        return function ($name) {
            $this->authManager = Api::manager($name);
        };
    }
}