<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Facades;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Illuminate\Validation\Validator;
use Myth\Api\ClientWrapper;
use Myth\Api\Interfaces\ResponseInterface;
use Myth\Api\ManagerWrapper;

/**
 * Class Api
 * @method static ManagerWrapper client(string $clientName)
 * @method static ResponseInterface sendToClient($client, $model, $body = [], $primaryKey = null)
 * @method static string modelToString($model)
 * @method static Builder clientData($client, $model, bool $sync = null)
 * @method static mixed syncWithClient($client, $client_id, $model)
 * @method static mixed syncedWithClient($client, $client_id, $model)
 * @method static mixed unsyncWithClient($client, $client_id, $model)
 * @method static string getManagerKeyName header manager key name
 * @method static string headerSRF header client token name
 * @method static string name Manager name
 * @method static string makeSecret Make a new client secret
 * @method static string secret get client secret
 * @method static string secretFromFile get client secret
 * @method static void routes client routes
 * @method static ClientWrapper manager(string $manager)
 * @method static string resolveRouteManagerConnection(Request $request)
 * @method static Builder managerData($manager, $model, bool $sync = null)
 * @method static Validator validateManagerRequest($data)
 * @method static string managerRequestKey manager array key from request
 * @method static string managerPrimaryKey manager primary key form request
 * @method static string clientResponseKey
 * @method static string clientPrimaryKey
 * @method static JsonResponse JsonResponse($data, $message, $status, $headers, $options)
 * @package Myth\Api\Facades
 */
class Api extends Facade
{

    /**
     * Get the registered name of the component.
     */
    public static function getFacadeAccessor() { return static::class; }
}
