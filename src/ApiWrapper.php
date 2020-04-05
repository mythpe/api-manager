<?php

namespace Myth\Api;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Myth\Api\Exceptions\ClientNotFoundException;
use Myth\Api\Exceptions\ManagerModelNotFoundException;
use Myth\Api\Exceptions\ManagerNotFoundException;
use Myth\Api\Exceptions\ManagerNotProvidedException;
use Myth\Api\Exceptions\PrimaryKeyException;
use Myth\Api\Exceptions\SecretNotSetupException;
use Myth\Api\Exceptions\SRFException;
use Myth\Api\Facades\Api;
use Myth\Api\Interfaces\ResponseInterface;
use Myth\Api\Models\ClientModel;
use Myth\Api\Models\ManagerModel;

/**
 * Class ApiWrapper
 * @package Myth\Api
 */
class ApiWrapper
{

    /** @var array Manager Config */
    protected $managerConfig = [];

    /** @var array Client Config */
    protected $clientConfig = [];

    /** @var string manager primary key from request */
    protected $managerRequestPrimaryKey = 'manager_id';

    /**
     * string request key of manager data will append to request while sync data with client
     * @var string
     */
    protected $managerRequestKey = 'myth_api_manager';

    /**
     * string request key of client data will append to response data after sync with manager
     * @var string
     */
    protected $clientRequestKey = 'myth_api_client';

    /** @var string client primary key in response */
    protected $clientRequestPrimaryKey = 'client_id';

    /** @var string $managerKeyName header manager key name */
    protected $managerKeyName = 'myth-name';

    /** @var string Secret File name */
    protected $secretFileName = "myth_api_client.key";

    /** @var string secret header key name */
    protected $headerSRF = 'myth-xsrf';

    /** @var array $middleware array for middleware */
    protected $middleware = ['api', 'myth.api.auth'];

    /** @var string $routeName name of client routes */
    protected $routeName = 'myth::';

    /** @var string $routePrefix route prefix of client connection */
    protected $routePrefix = 'myth';

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
     * Make new client secret
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function makeSecret(): string
    {
        $time = Carbon::now();
        $key = $this->clientConfig('secret');
        $name = $this->getSecretFileName();
        $this->disk()->put($name, urlencode(Crypt::encrypt(base64_encode(Str::random(40).$time.$key))));
        return $this->secret();
    }

    /**
     * @return string
     */
    public function getSecretFileName(): string
    {
        return $this->secretFileName;
    }

    /**
     * @return \Illuminate\Contracts\Filesystem\Filesystem|\Illuminate\Filesystem\FilesystemAdapter
     */
    public function disk()
    {
        return Storage::disk($this->clientConfig('file_system_disk'));
    }

    /**
     * get your client secret
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function secret(): string
    {
        return $this->secretFromFile();
    }

    /**
     * Get Secret From Secret File
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function secretFromFile(): string
    {
        return (string) !$this->disk()->exists($this->getSecretFileName()) ? "" : $this->disk()
            ->get($this->getSecretFileName());
    }

    /**
     * @param $modelUri
     * @return \Myth\Api\ManagerModelWrapper
     * @throws \Myth\Api\Exceptions\ManagerModelNotFoundException
     * @throws \Myth\Api\Exceptions\ManagerNotFoundException
     * @throws \Myth\Api\Exceptions\ManagerNotProvidedException
     * @throws \Myth\Api\Exceptions\SRFException
     * @throws \Myth\Api\Exceptions\SecretNotSetupException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function resolveRouteBinding($modelUri)
    {
        $manager = $this->resolveRouteManagerConnection(request());
        $manager = Api::manager($manager);
        if(!($model = $manager->getModelFromRoute($modelUri))){
            throw new ManagerModelNotFoundException();
        }

        return $manager->model($model);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string
     * @throws \Myth\Api\Exceptions\ManagerNotFoundException
     * @throws \Myth\Api\Exceptions\ManagerNotProvidedException
     * @throws \Myth\Api\Exceptions\SRFException
     * @throws \Myth\Api\Exceptions\SecretNotSetupException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function resolveRouteManagerConnection(Request $request): string
    {
        $appSecret = $this->secret();
        if(!$appSecret){
            throw new SecretNotSetupException();
        }
        $secretKey = $this->headerSRF();
        $headerToken = preg_replace('/\s+/', '', trim($request->header($secretKey)));
        if(!$request->hasHeader($secretKey) || $headerToken !== $appSecret){

            throw new SRFException();
        }
        $managerKey = $this->getManagerKeyName();
        if(!$request->hasHeader($managerKey)){
            throw new ManagerNotProvidedException();
        }
        $managerName = (string) preg_replace('/\s+/', '', trim($request->header($managerKey)));
        if(!$this->hasManager($managerName)){
            throw new ManagerNotFoundException();
        }
        return $managerName;
    }

    /**
     * @return string
     */
    public function headerSRF(): string
    {
        return $this->headerSRF;
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
     * check from manager name
     * @param $managerName
     * @return bool
     */
    public function hasManager($managerName): bool
    {
        return array_key_exists($managerName, $this->clientConfig['managers']);
    }

    /**
     * @param array $options
     */
    public function routes($options = []): void
    {
        Route::group($options, function (Router $router) {
            $router->group([
                "middleware" => $this->getMiddleware(),
                "as"         => $this->getRouteName(),
                "prefix"     => $this->getRoutePrefix(),
                "namespace"  => "\\Myth\\Api\\Controllers",
            ], function (Router $router) {
                $controller = "ApiClientController";
                $router->get('model/{MythApiManagerModel}', "{$controller}@index")->name('index');
                $router->post('model/{MythApiManagerModel}', "{$controller}@store")->name('store');
                $router->put('model/{MythApiManagerModel}', "{$controller}@update")->name('update');

                // $router->post('index-of', "{$controller}@indexOf")->name('indexOf');
                // $router->post('index-of-schema', "{$controller}@indexOfSchema")->name('indexOfSchema');
                // $router->post('index-of-schema-data', "{$controller}@indexOfSchemaData")->name('schemaData');
                // $router->get('index-of-entries', "{$controller}@indexOfEntries")->name('indexOfEntries');
                // $router->post('store-schema', "{$controller}@storeSchema")->name('storeSchema');
            });
        });
    }

    /**
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * @return string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * @return string
     */
    public function getRoutePrefix(): string
    {
        return $this->routePrefix;
    }

    /**
     * Get manager
     * @param $manager
     * @return \Myth\Api\ClientWrapper
     * @throws \Myth\Api\Exceptions\ManagerNotFoundException
     */
    public function manager($manager): ClientWrapper
    {
        if($manager instanceof ClientWrapper) return $manager;
        return new ClientWrapper($this->getManagerConfigs($manager), $manager);
    }

    /**
     * Get Manager config by manager name
     * @param string $name manager name
     * @return array
     * @throws \Myth\Api\Exceptions\ManagerNotFoundException
     */
    public function getManagerConfigs($name): array
    {
        try{
            return $this->clientConfig['managers'][$name];
        }
        catch(Exception $e){
            throw new ManagerNotFoundException();
        }
    }

    /**
     * @param string $manager
     * @param $model
     * @param null $sync
     * @return Builder
     */
    public function managerData(string $manager, $model, $sync = null): Builder
    {
        $model = $this->modelToString($model);
        return ManagerModel::managerData($manager, $model, $sync);
    }

    /**
     * Helper
     * @param $model
     * @return string
     */
    public function modelToString($model): string
    {
        return (string) !is_string($model) ? get_class($model) : $model;
    }

    /**
     * Get Client By name
     * @param $client
     * @return \Myth\Api\ManagerWrapper
     * @throws \Myth\Api\Exceptions\ClientNotFoundException
     */
    public function client($client): ManagerWrapper
    {
        if($client instanceof ManagerWrapper) return $client;
        return new ManagerWrapper($this->getClientConfigs($client), $client);
    }

    /**
     * Get Client config by client name
     * @param $name
     * @return array
     * @throws \Myth\Api\Exceptions\ClientNotFoundException
     */
    public function getClientConfigs($name): array
    {
        try{
            return $this->managerConfig['clients'][$name];
        }
        catch(Exception $e){
            throw new ClientNotFoundException();
        }
    }

    /**
     * @param $client
     * @param $model
     * @param array $body
     * @param null $primaryKey
     * @return \Myth\Api\Interfaces\ResponseInterface
     * @throws \Myth\Api\Exceptions\ClientNotFoundException
     * @throws \Myth\Api\Exceptions\PrimaryKeyException
     */
    public function sendToClient($client, $model, array $body = [], $primaryKey = null): ResponseInterface
    {
        if(!$client instanceof ManagerWrapper){
            $client = static::client($client);
        }
        if(!$model instanceof ClientModelWrapper){
            $model = $client->model($model);
        }

        is_null($primaryKey) && ($primaryKey = $model->model()->getKey());
        if(!$primaryKey){
            throw new PrimaryKeyException();
        }

        $body = $this->appendManagerToBody($body, $primaryKey);
        /** @var \GuzzleHttp\Psr7\Response $request */
        $request = $model->request('post', $this->buildModelUri($model->uri()), [
            'json' => $body,
        ]);
        $response = $request->getBody()->getContents();
        $array = json_decode($response, true) ? : [];
        $interface = new ResponseInterface($request, $array);
        if($primaryKey && ($client_id = $interface->client_id()) && ($model = $model->model()->find($primaryKey))){
            $clientStorage = $model->syncedWithClient($client->getClientName(), $client_id);
        }
        return $interface;
    }

    /**
     * @return string
     */
    public function managerRequestKey(): string
    {
        return $this->managerRequestKey;
    }

    /**
     * Get manager primary key from request
     * @return string
     */
    public function managerPrimaryKey(): string
    {
        return $this->managerRequestPrimaryKey;
    }

    /**
     * @return string
     */
    public function clientResponseKey(): string
    {
        return $this->clientRequestKey;
    }

    /**
     * Get manager primary key from request
     * @return string
     */
    public function clientPrimaryKey(): string
    {
        return $this->clientRequestPrimaryKey;
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
     * unsync data with client
     * Delete relation
     * @param $client
     * @param $client_id
     * @param $model
     * @return mixed
     * @throws \Myth\Api\Exceptions\ClientNotFoundException
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
     * @throws \Myth\Api\Exceptions\ClientNotFoundException
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
     * @throws \Myth\Api\Exceptions\ClientNotFoundException
     */
    public function syncedWithClient($client, $client_id, $model)
    {
        return $this->client($client)->syncedModel($client_id, $model);
    }

    /**
     * get api name
     * @return string
     */
    public function name()
    {
        return $this->getName();
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
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    public function validateManagerRequest($data = [])
    {
        $managerRequestKey = $this->managerRequestKey();
        $primaryKey = $this->managerPrimaryKey();
        return Validator::make($data, [
            "{$managerRequestKey}"               => ["required", 'array'],
            "{$managerRequestKey}.{$primaryKey}" => ["required", 'int'],
        ], [
            "{$managerRequestKey}.required" => "manager request key is required. {$managerRequestKey}",
        ]);
    }

    /**
     * get config from client config
     * @param string|null $key
     * @return array|mixed
     */
    public function clientConfig(string $key = null)
    {
        $config = $this->clientConfig;
        if(is_null($key)) return $config;
        return $config[$key];
    }

    /**
     * @param array $body
     * @param $id
     * @return array
     */
    public function appendManagerToBody($body, $id): array
    {
        !is_array($body) && ($body = []);
        $body[$this->managerRequestKey()] = [
            $this->managerPrimaryKey() => $id,
        ];
        return $body;
    }

    /**
     * @param $response
     * @param $id
     * @return array
     */
    public function appendClientToResponse($response, $id): array
    {
        !is_array($response) && ($response = []);
        $response[$this->clientResponseKey()] = [
            $this->clientPrimaryKey() => $id,
        ];
        return $response;
    }

    /**
     * Get Config from manager config
     * @param string $key
     * @return mixed
     */
    public function managerConfig(string $key = null)
    {
        $config = $this->managerConfig;
        if(is_null($key)) return $config;
        return $config[$key];
    }

    /**
     * Return static json response
     * @param array $data
     * @param string $message
     * @param int $status
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function jsonResponse(
        $data = [], $message = '', $status = 200, array $headers = [], $options = 0
    ): JsonResponse {

        $success = null;
        $errors = [];
        if(func_num_args() === 1 && is_array($data)){
            if(isset($data['errors'])){
                $errors = $data['errors'];
                unset($data['errors']);
            }
            if(isset($data['message'])){
                $message = $data['message'];
                unset($data['message']);
            }
            if(isset($data['success'])){
                $success = $data['success'];
                unset($data['success']);
            }
            if(isset($data['status'])){
                $status = $data['status'];
                unset($data['status']);
            }
            if(isset($data['options'])){
                $options = $data['options'];
                unset($data['options']);
            }
            if(isset($data['headers'])){
                $headers = $data['headers'];
                unset($data['headers']);
            }
            if(isset($data['data'])){
                $data = $data['data'];
                unset($data['data']);
            }
        }
        if(isset($data['errors'])){
            $errors = $data['errors'];
            unset($data['errors']);
        }
        $json = [
            "message" => (string) $message,
            "success" => (boolean) (is_null($success) ? ((int) $status === 200) : $success),
            "status"  => (int) $status,
            "data"    => $data,
            "errors"  => $errors,
        ];

        return $this->response()->json($json, $status, $headers, $options)->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    /**
     * Return a new response from the application.
     * @param \Illuminate\View\View|string|array|null $content
     * @param int $status
     * @param array $headers
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function response($content = '', $status = 200, array $headers = [])
    {
        $factory = app(ResponseFactory::class);

        if(func_num_args() === 0){
            return $factory;
        }

        return $factory->make($content, $status, $headers);
    }

    /**
     * @param string $uri
     * @return string
     */
    protected function buildModelUri(string $uri = ''): string
    {
        return "myth/model/".ltrim($uri);
    }

}