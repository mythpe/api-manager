<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api;

use Illuminate\Database\Eloquent\Builder;
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
    protected $name = '';

    /** @var array $config Client config */
    protected $config = [];

    /** @var string $secret Client secret */
    protected $secret = '';

    /** @var string $baseUri Client API base URL */
    protected $baseUri = '';

    /** @var array[] $models list of locale models must will be sync with client */
    protected $models = [];

    /** @var array[] $options Client options */
    protected $options = [];

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

        /** @var HttpClient http */
        $this->http = new HttpClient($this->getOption('http', []));

        $this->http->setOption('base_uri', $this->getBaseUri());

        /** set header manager key name */
        $this->http->setManagerName(Api::name());

        /** set token client */
        $this->http->setClientToken($this->secret);
    }

    /**
     * @param null|string $append
     * @return string
     */
    public function getBaseUri(string $append = null): string
    {
        return $this->baseUri.(!is_null($append) ? ltrim($append, '/') : "");
    }

    /**
     * @param string $baseUri
     * @return ManagerWrapper
     */
    public function setBaseUrl(string $baseUri): ManagerWrapper
    {
        $this->baseUri = rtrim($baseUri, '/')."/";
        return $this;
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
     * @param string $secret
     * @return ManagerWrapper
     */
    public function setSecret(string $secret): ManagerWrapper
    {
        $this->secret = $secret;
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
     * Get model client locale storage data
     * @param $model
     * @param null $sync
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function data($model, $sync = null): Builder
    {
        return $this->model($model)->data($sync);
    }

    /**
     * @param mixed $model
     * @param array|null $body
     * @param $primaryKey
     * @return ResponseInterface
     */
    public function sendData($model, array $body = null, $primaryKey = null): ResponseInterface
    {
        $model = $this->model($model);
        if(is_null($body)){
            $body = $model->transformer()->body();
        }
        return Api::sendToClient($this, $model, $body, $primaryKey);
    }

    /**
     * get specific locale model config of client
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
     * @return array[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array[] $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @param null $key
     * @param null $default
     * @return array|array[]
     */
    public function getOption($key = null, $default = null)
    {
        if(is_null($key)) return $this->getOptions();
        return array_key_exists($key, $this->options) ? $this->options[$key] : $default;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setOption(string $key, $value): void
    {
        $this->options[$key] = $value;
    }

    /**
     * @param null $key
     * @param null $value
     * @return $this|array|array[]|mixed
     */
    public function option($key = null, $value = null)
    {
        if(is_null($key)) return $this->getOptions();
        if(is_null($value)) return $this->getOption($key);
        $this->setOption($key, $value);
        return $this;
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
        $this->setBaseUrl($config['base_uri']);
        $this->models = $this->config['models'];
        $this->options = $this->config['options'];
    }
}