<?php

namespace Myth\Api\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class ManagerModel
 * @method static ManagerModel|Builder Sync
 * @method static ManagerModel|Builder Synced
 * @method static Builder ManagerModelScope($model, $manager, $manager_id)
 * @package Myth\Api\Models
 */
class ManagerModel extends Model
{

    protected $table = "myth_api_manager_models";

    protected $fillable = [
        'manager_name',
        'manager_id',
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
     * @param string $manager
     * @param $model
     * @param bool|null $sync
     * @return mixed
     */
    public static function managerData(string $manager, $model, $sync = null)
    {
        return $model::ByManager($manager, $sync);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param $model
     * @param $manager
     * @param $manager_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeManagerModelScope(Builder $builder, $model, $manager, $manager_id)
    {
        return $builder->where([
            'manager_name'  => $manager,
            'manager_id'    => $manager_id,
            'syncable_type' => $model,
        ]);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param $model
     * @param $manager
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByManager(Builder $builder, $model, $manager)
    {
        return $builder->where([
            'manager_name'  => $manager,
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
     * locale manager data
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function syncable()
    {
        return $this->morphTo();
    }
}
