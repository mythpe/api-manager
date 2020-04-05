<?php

namespace Myth\Api;

use Myth\Api\Facades\Api;
use Myth\Api\Helpers\HttpClient;
use Myth\Api\Interfaces\ResponseInterface;

class ClientModelWrapper
{

    /** @var ManagerWrapper $client client wrapper */
    protected $client;

    /** @var string locale model class name */
    protected $modelClassName;

    /** @var mixed $model locale model object */
    protected $model;

    /** @var array $config model config */
    protected $config = [];

    /**
     * ClientModelWrapper constructor.
     * @param \Myth\Api\ManagerWrapper|string $client
     * @param mixed $model locale model String|Object
     */
    public function __construct($client, $model)
    {
        $this->client = Api::client($client);
        $this->modelClassName = !is_string($model) ? get_class($model) : $model;
        $this->model = is_string($model) ? app($model) : $model;
        $this->config = $this->client->getModelConfig($this->modelClassName);
    }

    /**
     * @return string
     */
    public function modelClassName(): string
    {
        return $this->modelClassName;
    }

    /**
     * Get model transformer
     * @return mixed
     */
    public function transformer()
    {
        return new $this->config['transformer']($this->model());
    }

    /**
     * Get client
     * @return ManagerWrapper
     */
    public function client(): ManagerWrapper
    {
        return $this->client;
    }

    /**
     * Helper
     * get locale model object
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * get locale model uri of client
     * @return string
     */
    public function uri(): string
    {
        return $this->config['uri'];
    }

    /**
     * @return Helpers\HttpClient
     */
    public function http(): HttpClient
    {
        return $this->client->getHttp();
    }

    /***
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function request()
    {
        return $this->http()->client()->request(...func_get_args());
    }

    /**
     * @return ResponseInterface
     */
    public function sendToClient(): ResponseInterface
    {
        $body = $this->transformer()->body();
        return $response = Api::sendToClient($this->client(), $this, $body);
    }

    /**
     * Get model client locale storage data
     * @param null $sync
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function data($sync = null)
    {
        return Api::clientData($this->client->getClientName(), $this->modelClassName(), $sync);
    }
}