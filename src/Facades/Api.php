<?php

namespace Myth\Api\Facades;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Myth\Api\ClientModelWrapper;
use Myth\Api\Interfaces\ResponseInterface;
use Myth\Api\ManagerWrapper;
use Myth\Api\Models\ClientModel;

/**
 * Class Api
 * @method static ManagerWrapper client(string $clientName)
 * @method static string getClientModelUri(ManagerWrapper|string $client, $model)
 * @method static ResponseInterface sendToClient(ManagerWrapper|string $client, string|ClientModelWrapper|Model $model, array $body = null)
 * @method static string modelToString( $model)
 * @method static Builder clientData($client, $model, bool $sync = null)
 * @method static mixed syncWithClient($client, $client_id, $model)
 * @method static mixed syncedWithClient($client, $client_id, $model)
 * @method static mixed unsyncWithClient($client, $client_id, $model)
 * @method static string getManagerKeyName header manager key name
 * @method static string getTokenName header client token name
 * @method static string name Manager name
 * @package Myth\Api\Facades
 */
class Api extends Facade
{

    /**
     * Get the registered name of the component.
     */
    public static function getFacadeAccessor() { return static::class; }
}
