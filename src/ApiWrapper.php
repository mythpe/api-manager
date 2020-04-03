<?php

namespace Myth\Api;

use Myth\Api\Wrappers\ApiWrapperHelper;
use Myth\Api\Wrappers\ManagerWrapperTrait;
use Myth\Api\Wrappers\ResponseTrait;

/**
 * Class ApiWrapper
 * @package Myth\Api
 */
class ApiWrapper
{

    use ApiWrapperHelper;
    use ResponseTrait;
    use ManagerWrapperTrait;

    /** @var array Manager Config */
    protected $managerConfig;
    /** @var array Client Config */
    protected $clientConfig;
    /** @var string $authentication header authentication name */
    protected $tokenName = 'MYTH-XSRF';

    /**
     * ApiWrapper constructor.
     * @param $managerConfig
     * @param $clientConfig
     */
    public function __construct($managerConfig, $clientConfig)
    {
        $this->managerConfig = $managerConfig;
        $this->clientConfig = $clientConfig;
    }

    /**
     * get API header authentication name
     * @return string
     */
    public function getTokenName(): string
    {
        return $this->tokenName;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->getName();
    }
}