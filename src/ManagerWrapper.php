<?php

namespace Myth\Api;

use Myth\Api\Facades\Api;
use Myth\Api\Helpers\HttpClient;
use Myth\Api\Interfaces\ResponseInterface;

/**
 * Class ManagerWrapper
 * @package Myth\Api
 */
class ManagerWrapper
{

    /** @var string $name Client name */
    protected $name;
    /** @var array $config Client config */
    protected $config;
    /** @var string $secret Client secret */
    protected $secret;
    /** @var string $baseUrl Client API base URL */
    protected $baseUrl;
    /** @var array[] $models list of locale models must will be sync with client */
    protected $models;
    /** @var array[] $options Client options */
    protected $options;
    /** @var HttpClient $http client */
    protected $http;

    /**
     * ManagerWrapper constructor.
     * @param $config array client config
     * @param $name string client name
     */
    public function __construct($config, $name)
    {
        /** fill client config */
        $this->fillClientConfig($config, $name);
        $options = isset($config['options']['http']) ? $config['options']['http'] : [];
        /** @var HttpClient http */
        $this->http = new HttpClient($options);
        /** set header manager key name */
        $this->http->setManagerName(Api::name());
        /** set token client */
        $this->http->setClientToken($this->secret);
    }

    /**
     * Get client name
     * @return string
     */
    public function getClientName(): string
    {
        return $this->name;
    }

    /**
     * @return HttpClient
     */
    public function getHttp(): HttpClient
    {
        return $this->http;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     * @return ManagerWrapper
     */
    public function setSecret(string $secret): ManagerWrapper
    {
        $this->secret = $secret;
        return $this;
    }

    /**
     * @param null|string $append
     * @return string
     */
    public function getBaseUrl(string $append = null): string
    {
        return rtrim($this->baseUrl, '/').(!is_null($append) ? "/".ltrim($append, '/') : "");
    }

    /**
     * @param string $baseUrl
     * @return ManagerWrapper
     */
    public function setBaseUrl(string $baseUrl): ManagerWrapper
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        return $this;
    }

    /**
     * Get new model wrapper of client
     * @param $model
     * @return ClientModelWrapper
     */
    public function model($model): ClientModelWrapper
    {
        return new ClientModelWrapper($this->getClientName(), $model);
    }

    /**
     * @param mixed $model
     * @param array|null $body
     * @return ResponseInterface
     */
    public function sendData($model, array $body = null): ResponseInterface
    {
        $model = $this->model($model);
        if(is_null($body)){
            $body = $model->transformer()->body();
        }
        return Api::sendToClient($this, $model, $body);
    }

    /**
     * get specific locale model config for client
     * @param $model
     * @return array
     */
    public function getModelConfig($model): array
    {
        $k = !is_string($model) ? get_class($model) : $model;
        return $this->models[$k];
    }

    /**
     * unsync data with client
     * delete row
     * @param $client_id
     * @param $model
     * @return mixed
     */
    public function unsyncModel($client_id, $model)
    {
        return $this->model($model)->model()->unsyncWithClient($this->getClientName(), $client_id);
    }

    /**
     * update or create relation with locale model
     * @param $client_id
     * @param $model
     * @return mixed
     */
    public function syncModel($client_id, $model)
    {
        return $this->model($model)->model()->syncWithClient($this->getClientName(), $client_id);
    }

    /**
     * set synced with client
     * @param $client_id
     * @param $model
     * @return mixed
     */
    public function syncedModel($client_id, $model)
    {
        return $this->model($model)->model()->syncedWithClient($this->getClientName(), $client_id);
    }

    /**
     * Fill client config data into class
     * @param $config
     * @param $name
     */
    protected function fillClientConfig($config, $name): void
    {
        $this->config = $config;
        $this->name = $name;
        $this->setSecret($this->config['secret']);
        $this->setBaseUrl($config['base_url']);
        $this->models = $this->config['models'];
        $this->options = $this->config['options'];
    }
}

/**
 * 1- send new data
 * 2- get list of data
 * 3- update data
 */