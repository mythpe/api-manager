<?php

namespace Myth\Api\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Myth\Api\Facades\Api;
use Myth\Api\Interfaces\ResponseInterface;
use Myth\Api\ManagerWrapper;
use Myth\Api\Models\ClientModel;
use Myth\Api\Models\ManagerModel;

/**
 * Trait HasMythApi
 * @package Myth\Api\Traits
 */
trait HasMythApi
{

    /**
     *
     */
    protected static function bootHasMythApi()
    {
        static::saved(function ($model) {
            if($model->client_storage()->exists()){
                $model->client_storage()->update([
                    'sync' => true,
                ]);
            }

            if($model->manager_storage()->exists()){
                $model->manager_storage()->update([
                    'sync' => true,
                ]);
            }
        });
        static::deleted(function ($model) {
            $model->manager_storage()->delete();
            $model->client_storage()->delete();
        });
    }

    /*
   |--------------------------------------------------------------------------
   | Client area
   |--------------------------------------------------------------------------
   |
   | This area will used by manager
   |
   */

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $client
     * @param null|bool $sync
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByClient(Builder $builder, $client, $sync = null)
    {
        return $builder->whereHas('client_storage', function (Builder $builder) use ($client, $sync) {
            $builder = $builder->where('client_name', $client);
            if(!is_null($sync)){
                $builder = $builder->where('sync', $sync);
            }
            return $builder;
        });
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
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function client_storage()
    {
        return $this->morphMany(ClientModel::class, 'syncable');
    }

    /**
     * @param string $client
     * @param $client_id
     * @return void
     */
    public function deleteAllClientRelations(string $client, $client_id): void
    {
        $this->client_storage()->where('client_name', $client)->delete();
        ManagerModel::ManagerModelScope(static::class, $client, $client_id)->delete();
    }

    /**
     * set synced with client
     * @param $client
     * @param $client_id
     * @return $this
     */
    public function syncedWithClient($client, $client_id)
    {
        $this->deleteAllClientRelations($client, $client_id);
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
        $this->deleteAllClientRelations($client, $client_id);
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

    /*
   |--------------------------------------------------------------------------
   | Manager area
   |--------------------------------------------------------------------------
   |
   | This area will used by client
   |
   */

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $manager
     * @param null|bool $sync
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByManager(Builder $builder, $manager, $sync = null)
    {
        return $builder->whereHas('manager_storage', function (Builder $builder) use ($manager, $sync) {
            $builder = $builder->where('manager_name', $manager);
            if(!is_null($sync)){
                $builder = $builder->where('sync', $sync);
            }
            return $builder;
        });
    }

    /**
     * @param $manager
     * @param bool|null $sync
     * @return Builder
     */
    public static function managerData($manager, $sync = null): Builder
    {
        return Api::managerData($manager, static::class, $sync);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function manager_storage()
    {
        return $this->morphMany(ManagerModel::class, 'syncable');
    }

    /**
     * @param string $manager
     * @param $manager_id
     * @return void
     */
    public function deleteAllManagerRelations(string $manager, $manager_id): void
    {
        $this->manager_storage()->where('manager_name', $manager)->delete();
        ManagerModel::ManagerModelScope(static::class, $manager, $manager_id)->delete();
    }

    /**
     * set synced with manager
     * @param $manager
     * @param $manager_id
     * @return $this
     */
    public function syncedWithManager($manager, $manager_id)
    {
        $this->deleteAllManagerRelations($manager, $manager_id);
        $this->manager_storage()->updateOrCreate([
            "manager_name" => $manager,
            "manager_id"   => $manager_id,
        ], [
            'sync'      => false,
            'sync_time' => Carbon::now(),
        ]);
        return $this;
    }

    /**
     * set must sync with manager
     * @param $manager
     * @param $manager_id
     * @return $this
     */
    public function syncWithManager($manager, $manager_id)
    {
        $this->deleteAllManagerRelations($manager, $manager_id);
        $this->manager_storage()->updateOrCreate([
            "manager_name" => $manager,
            "manager_id"   => $manager_id,
        ], [
            'sync' => true,
        ]);
        return $this;
    }

    /**
     * unsync model with manager
     * Delete relation
     * @param $manager
     * @param $manager_id
     * @return $this
     */
    public function unsyncWithManager($manager, $manager_id)
    {
        $this->manager_storage()->where([
            "manager_name" => $manager,
            "manager_id"   => $manager_id,
        ])->delete();
        return $this;
    }
}