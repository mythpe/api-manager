<?php

namespace Myth\Api\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Myth\Api\Client\Entities\ApiClientRelation;
use Myth\Api\Client\Facades\Client;
use Myth\Api\Client\Traits\HasMythApiClientTrait;
use Myth\Api\Facades\Api;

class BaseController extends Controller
{

    use ValidatesRequests;

    /** @var int $page pagination */
    protected $page;

    /** @var int $itemsPerPage pagination items */
    protected $itemsPerPage;

    /** @var Request */
    protected $request;

    /** @var String */
    protected $managerKey;

    /** @var bool */
    protected $debugMode = false;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->itemsPerPage = (int) $this->request->get("itemsPerPage", 15);
        $this->page = (int) $this->request->get("page", 1);
        $this->managerKey = Api::managerPrimaryKey();
        $this->debugMode = (boolean) $this->request->get("debug", false);
    }

    public function debug($value = null)
    {
        if(!is_null($value)) $this->debugMode = (bool) $value;
        return $this->debugMode;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexOf()
    {
        $t = [];
        try{
            $f = DB::select('SHOW TABLES');
            $facade = collect(json_decode(collect($f)->toJson(), true))->map(function ($v) {
                return is_array($v) ? current($v) : $v;
            });
            foreach($facade as $a){
                $t[] = $a;
            }
        }
        catch(\Exception $exception){
            $t = [];
        }
        return Client::jsonResponse($t);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexOfSchema()
    {
        $response = [];
        try{
            $response = Schema::getColumnListing($this->request->post('key'));
        }
        catch(Exception $exception){
            $response = [];
        }
        return Client::jsonResponse($response);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexOfSchemaData()
    {
        try{
            $response = new class extends Model{

                use HasMythApiClientTrait;

                protected $table;

                public function __construct(array $attributes = [])
                {
                    parent::__construct($attributes);
                    $this->table = request()->post('key');
                }
            };
            $response = $response->all()->toArray();
        }
        catch(Exception $exception){
            $response = [];
        }
        return Client::jsonResponse($response);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexOfEntries()
    {
        $response = [];
        try{
            $response = Client::getEntities();
        }
        catch(Exception $exception){
            $response = [];
        }
        return Client::jsonResponse($response);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function storeSchema()
    {
        /** validation on tamweelk primary key */
        $this->validateManagerPrimaryKey();
        $model = new class extends Model{

            use HasMythApiClientTrait;

            protected $table;

            public function __construct(array $attributes = [])
            {
                parent::__construct($attributes);
                $this->table = request()->get('key');
            }
        };
        $this->validate($this->request, [
            'data'                    => ['required', 'array'],
            'sync_data'               => ['required', 'array'],
            'sync_data.syncable_type' => ['required'],
        ]);
        $model->setRawAttributes($this->request->get('data'));
        !$this->debugMode && $model->save();
        $sync_data = [
            Client::getManagerPrimaryKeyFromRequest() => $this->getManagerPrimaryKey(),
            "manager_name"                            => Client::getManagerName(),
            "must_sync"                               => true,
            "syncable_id"                             => $model->id,
            "syncable_type"                           => $this->request->get('sync_data', [])['syncable_type'],
        ];
        $sync = ApiClientRelation::newModelInstance()->setRawAttributes($sync_data);
        if(!$this->debugMode){
            $sync->save();
            $model->refresh();
            $sync->refresh();
        }
        $data = array_merge($model->appendRelations($model->appendToMythApiArray()), [
            'myth_api_data' => $sync->toArray(),
        ]);
        return Client::jsonResponse($data);
    }

    /**
     * @return mixed
     */
    public function getManagerPrimaryKey()
    {
        return ($r = $this->request->get($this->managerKey, 0)) ? $r : null;
    }

    public function response($data = [], $message = '', $status = 200, array $headers = [], $options = 0)
    {
        return Api::JsonResponse($data, $message, $status, $headers, $options);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateManagerPrimaryKey(): void
    {
        Api::validateManagerRequest($this->request->all())->validate();
    }

    /**
     * pluck manager id from request
     * @return int
     */
    protected function pluckManagerId(): int
    {
        try{
            $data = $this->request->get(Api::managerRequestKey());
            return $data[Api::managerPrimaryKey()];
        }
        catch(\Exception $exception){
        }
        return 0;
    }
}
