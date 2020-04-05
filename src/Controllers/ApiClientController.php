<?php

namespace Myth\Api\Controllers;

use Exception;
use Illuminate\Validation\ValidationException;
use Myth\Api\Exceptions\ManagerRequestValidatorException;
use Myth\Api\Facades\Api;
use Myth\Api\ManagerModelWrapper as Model;
use Myth\Api\Models\ManagerModel;
use Myth\Api\Resources\CollectionResponse;

/**
 * Class ApiClientController
 * @package Myth\Api\Controllers
 */
class ApiClientController extends BaseController
{

    /**
     * @param \Myth\Api\ManagerModelWrapper $model
     * @return \Myth\Api\Resources\CollectionResponse
     */
    public function index(Model $model)
    {
        $query = $model->data($this->request->get('sync', null));

        ($this->itemsPerPage === -1) && ($this->itemsPerPage = $query->count());
        $query = $query->paginate((int) $this->itemsPerPage, '*', 'page', (int) $this->page);
        return new CollectionResponse($query);
    }

    /**
     * @param \Myth\Api\ManagerModelWrapper $model
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Model $model)
    {
        /** validation on primary key */
        $this->validateManagerPrimaryKey();
        $data = [];
        try{
            $model->model()->setRawAttributes($model->fillable());
            if(($result = $model->validate($this->request)) !== true){
                throw new ManagerRequestValidatorException($result);
            }
            $model->saving($this->request);
            if(!$this->debug()){
                if($model->model()->save()){
                    $model->model()->refresh();
                    $model->model()->syncedWithManager($model->manager()->getName(), $this->pluckManagerId());
                    $model->saved($this->request);
                }
            }
        }
        catch(Exception $exception){
            $data = [];
            $message = $exception->getMessage();
            if($exception instanceof ValidationException){
                $data['errors'] = $exception->errors();
            }
            !$message && ($message = "server error !!");
            return $this->response($data, $message, 422);
        }
        if($model->model()->exists){
            $data = $model->toArray();
            $data = Api::appendClientToResponse($data, $model->model()->id);
        }
        return $this->response($data);
    }

    /**
     * @param \Myth\Api\ManagerModelWrapper $model
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Model $model)
    {
        $managerRequestKey = Api::managerRequestKey();
        $primaryKey = Api::managerPrimaryKey();
        /** validation on primary key */
        $this->validate($this->request, [
            "{$managerRequestKey}"   => ["array", "required"],
            "{$managerRequestKey}.*" => ["required_with:{$primaryKey}", "integer"],
        ]);
        $ids = collect($this->request->get($managerRequestKey))->unique()->values()->toArray();
        $managerData = 0;
        if(!$this->debug()) $managerData = ManagerModel::ByManager($model->getModelClassName(), $model->managerName())
            ->where('sync', true)
            ->whereIn('manager_id', $ids)
            ->update([
                'sync' => false,
            ]);

        return $this->response(['managerData' => $managerData], "Updated Data: {$managerData}");
    }
}