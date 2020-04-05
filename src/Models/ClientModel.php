<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class ClientModel
 * @method static ClientModel|Builder Sync
 * @method static ClientModel|Builder Synced
 * @package Myth\Api\Models
 */
class ClientModel extends Model
{

    /**
     * @var string
     */
    protected $table = "myth_api_client_models";

    /**
     * @var array
     */
    protected $fillable = [
        'client_name',
        'client_id',
        'syncable_type',
        'syncable_id',
        'sync',
        'sync_time',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'sync' => false,
    ];

    /**
     * @var array
     */
    protected $casts = [
        'sync' => 'boolean',
    ];

    /**
     * @var array
     */
    protected $dates = ['sync_time'];

    /**
     * @param string $client
     * @param $model
     * @param bool|null $sync
     * @return mixed
     */
    public static function clientData(string $client, $model, $sync = null)
    {
        return $model::ByClient($client, $sync);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param $model
     * @param $client
     * @param $client_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClientModelScope(Builder $builder, $model, $client, $client_id)
    {
        return $builder->where([
            'client_name'   => $client,
            'client_id'     => $client_id,
            'syncable_type' => $model,
        ]);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param $model
     * @param $client
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByClient(Builder $builder, $model, $client)
    {
        return $builder->where([
            'client_name'   => $client,
            'syncable_type' => $model,
        ]);
    }

    /**
     * get must sync
     * @param Builder $builder
     * @return Builder
     */
    public function scopeSync(Builder $builder): Builder
    {
        return $builder->where([
            "sync" => true,
        ]);
    }

    /**
     * get synced
     * @param Builder $builder
     * @return Builder
     */
    public function scopeSynced(Builder $builder): Builder
    {
        return $builder->where([
            "sync" => false,
        ]);
    }

    /**
     * locale client data
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function syncable()
    {
        return $this->morphTo();
    }
}
