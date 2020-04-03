<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Myth\Api\Facades\Api;
use Myth\Api\Interfaces\ResponseInterface;
use Myth\Api\ManagerWrapper;
use Myth\Api\Models\ClientModel;

trait HasApiManager
{

    /**
     * @param $client
     * @param bool|null $sync
     * @return Builder
     */
    public static function clientData($client, $sync = null): Builder
    {
        return Api::clientData($client, static::class, $sync);
    }

    /**
     * sync model to client
     * @param ManagerWrapper|string $client
     * @return ResponseInterface
     */
    public function sendToClient($client): ResponseInterface
    {
        $response = Api::sendToClient($client, $this);
        return $response;
    }

    /**
     * set relation local storage
     * @param string $client
     * @param int $client_id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function setClientLocaleStorage($client, $client_id)
    {
        return $a = $this->client_storage()->firstOrCreate([
            'client_name' => $client,
            'client_id'   => $client_id,
        ], [
            'sync'      => false,
            'sync_time' => Carbon::now(),
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function client_storage()
    {
        return $this->morphOne(ClientModel::class, 'syncable');
    }

    /**
     * set synced with client
     * @param $client
     * @param $client_id
     * @return $this
     */
    public function syncedWithClient($client, $client_id)
    {
        $this->client_storage()->updateOrCreate([
            "client_name" => $client,
            "client_id"   => $client_id,
        ], [
            'sync'      => false,
            'sync_time' => Carbon::now(),
        ]);
        return $this;
    }

    /**
     * set must sync with client
     * @param $client
     * @param $client_id
     * @return $this
     */
    public function syncWithClient($client, $client_id)
    {
        $this->client_storage()->updateOrCreate([
            "client_name" => $client,
            "client_id"   => $client_id,
        ], [
            'sync' => true,
        ]);
        return $this;
    }

    /**
     * unsync model with client
     * Delete relation
     * @param $client
     * @param $client_id
     * @return $this
     */
    public function unsyncWithClient($client, $client_id)
    {
        $this->client_storage()->where([
            "client_name" => $client,
            "client_id"   => $client_id,
        ])->delete();
        return $this;
    }

}