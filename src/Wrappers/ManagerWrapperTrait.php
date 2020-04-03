<?php

namespace Myth\Api\Wrappers;

use Illuminate\Database\Eloquent\Builder;
use Myth\Api\ClientModelWrapper;
use Myth\Api\Interfaces\ResponseInterface;
use Myth\Api\ManagerWrapper;
use Myth\Api\Models\ClientModel;
use Myth\Api\Traits\HasApiManager;

trait ManagerWrapperTrait
{

    /** @var string $managerKeyName header manager key name */
    protected $managerKeyName = 'MYTH-NAME';

    /**
     * Get Client By name
     * @param $client
     * @return ManagerWrapper
     */
    public function client($client): ManagerWrapper
    {
        if($client instanceof ManagerWrapper) return $client;
        return new ManagerWrapper($this->getClientConfigs($client), $client);
    }

    /**
     * get full model uri
     * @param ManagerWrapper|string $client client name
     * @param mixed $model locale model object
     * @return string
     */
    public function getClientModelUri($client, $model): string
    {

        if(!$client instanceof ManagerWrapper){
            $client = static::client($client);
        }
        return $client->getBaseUrl($client->model($model)->uri());
    }

    /**
     * @param ManagerWrapper|string $client
     * @param \Myth\Api\ClientModelWrapper|string $model
     * @param array $body
     * @return array
     */
    public function sendToClient($client, $model, array $body = []): ResponseInterface
    {
        if(!$client instanceof ManagerWrapper){
            $client = static::client($client);
        }
        if(!$model instanceof ClientModelWrapper){
            $model = $client->model($model);
        }

        $url = $model->getFullUri();
        /** @var \GuzzleHttp\Psr7\Response $request */
        $request = $model->request('post', $url, [
            'json' => $body,
        ]);
        $response = $request->getBody()->getContents();
        $array = json_decode($response, true);
        $interface = new ResponseInterface($request, $array);

        if(($model = $model->model()) && $model->exists){
            $clientStorage = $model->setClientLocaleStorage($client->getClientName(), $interface->client_id());
        }
        return $interface;
    }

    /**
     * header manager key name
     * @return string
     */
    public function getManagerKeyName(): string
    {
        return $this->managerKeyName;
    }

    /**
     * @param string $client
     * @param $model
     * @param null $sync
     * @return Builder
     */
    public function clientData(string $client, $model, $sync = null): Builder
    {
        $model = $this->modelToString($model);
        return ClientModel::clientData($client, $model, $sync);
    }

    /**
     * Base Name
     * Get manager name from manager config
     * @return string
     */
    public function getName(): string
    {
        return $this->managerConfig('name');
    }

    /**
     * unsync data with client
     * Delete relation
     * @param $client
     * @param $client_id
     * @param $model
     * @return mixed
     */
    public function unsyncWithClient($client, $client_id, $model)
    {
        return $this->client($client)->unsyncModel($client_id, $model);
    }

    /**
     * set must sync with client
     * @param $client
     * @param $client_id
     * @param $model
     * @return mixed
     */
    public function syncWithClient($client, $client_id, $model)
    {
        return $this->client($client)->syncModel($client_id, $model);
    }

    /**
     * set synced with client
     * @param $client
     * @param $client_id
     * @param $model
     * @return mixed
     */
    public function syncedWithClient($client, $client_id, $model)
    {
        return $this->client($client)->syncedModel($client_id, $model);
    }

    /**
     * Get Client config by client name
     * @param $name
     * @return array
     */
    protected function getClientConfigs($name): array
    {
        return $this->managerConfig['clients'][$name];
    }

    /**
     * Get Config from manager config
     * @param string $key
     * @return mixed
     */
    protected function managerConfig(string $key = null)
    {
        $config = $this->managerConfig;
        if(is_null($key)) return $config;
        return $config[$key];
    }
}