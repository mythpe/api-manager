<?php

namespace Myth\Api;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Myth\Api\Facades\Api;
use Myth\Api\Models\ManagerModel;
use Myth\Api\Transformer\ManagerTransformer;

class ManagerModelWrapper
{

    /** @var ClientWrapper manager wrapper */
    protected $manager;

    /** @var string locale model class name */
    protected $modelClassName;

    /** @var mixed $model locale model object */
    protected $model;

    /** @var array model config */
    protected $config = [];

    /** @var ManagerTransformer */
    protected $transformer;

    /**
     * ManagerModelWrapper constructor.
     * @param ClientWrapper|string $manager
     * @param mixed $model locale model String|Object
     */
    public function __construct($manager, $model)
    {
        $this->manager = Api::manager($manager);
        $this->modelClassName = !is_string($model) ? get_class($model) : $model;
        $this->model = is_string($model) ? app($model) : $model;
        $this->config = $this->manager->getModelConfig($this->modelClassName);
        // $this->transformer = new $this->config['transformer']($this->model);
        $this->transformer = $this->config['transformer'];
    }

    /**
     * @return string
     */
    public function getModelClassName(): string
    {
        return $this->modelClassName;
    }

    /**
     * @return string
     */
    public function modelClassName(): string
    {
        return $this->modelClassName;
    }

    /**
     * Get manager
     * @return ClientWrapper
     */
    public function manager(): ClientWrapper
    {
        return $this->manager;
    }

    /**
     * get locale model object
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * @param $manager_id
     * @return bool
     */
    public function exist($manager_id): bool
    {
        return ManagerModel::ManagerModelScope($this->modelClassName(), $this->managerName(), $manager_id)->exists();
    }

    /**
     * @param $manager_id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function dataOf($manager_id)
    {
        return ManagerModel::ManagerModelScope($this->modelClassName(), $this->managerName(), $manager_id)->get();
    }

    /**
     * @param $manager_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function modelScope($manager_id): Builder
    {
        return ManagerModel::ManagerModelScope($this->modelClassName(), $this->managerName(), $manager_id);
    }

    /**
     * @return string
     */
    public function managerName()
    {
        return $this->manager()->getName();
    }

    /**
     * get locale model uri of manager
     * @return string
     */
    public function uri(): string
    {
        return $this->config['uri'];
    }

    /**
     * Get manager locale storage data
     * @param null $sync
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function data($sync = null)
    {
        return Api::managerData($this->manager->getName(), $this->modelClassName(), $sync);
    }

    public function fillable()
    {
        return $this->transformer::fillable($this->model(), request());
    }

    public function toArray()
    {
        return $this->transformer::toArray($this->model(), request());
    }

    public function validate(Request $request)
    {
        return $this->transformer::validate($request, $this->model());
    }

    public function saving(Request $request)
    {
        return $this->transformer::saving($this->model(), $request);
    }

    public function saved(Request $request)
    {
        return $this->transformer::saved($this->model(), $request);
    }
}