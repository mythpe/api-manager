<?php

namespace Myth\Api\Models;

use Illuminate\Database\Eloquent\Builder;
use Myth\Api\Facades\Api;

/**
 * Class ClientModel
 * @method static ClientModel|Builder Sync
 * @method static ClientModel|Builder Synced
 * @package Myth\Api\Models
 */
class ClientModel extends Model
{

    protected $table = "myth_api_client_models";
    protected $fillable = [
        'client_name',
        'client_id',
        'syncable_type',
        'syncable_id',
        'sync',
        'sync_time',
    ];
    protected $attributes = [
        'sync' => false,
    ];
    protected $casts = [
        'sync' => 'boolean',
    ];
    protected $dates = ['sync_time'];

    /**
     * @param string $client
     * @param $model
     * @param bool|null $sync
     * @return mixed
     */
    public static function clientData(string $client, $model, $sync = null)
    {
        $model = Api::modelToString($model);
        return $model::query()->whereHas('client_storage', function (Builder $builder) use ($client, $sync) {
            $builder = $builder->where('client_name', $client);
            if(!is_null($sync)){
                $builder = $builder->where('sync', $sync);
            }
            return $builder;
        });
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
