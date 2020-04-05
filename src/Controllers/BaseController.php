<?php

namespace Myth\Api\Controllers;

use Exception;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Myth\Api\Facades\Api;

/**
 * Class BaseController
 * @package Myth\Api\Controllers
 */
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

    /**
     * BaseController constructor.
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->itemsPerPage = (int) $this->request->get("itemsPerPage", 15);
        $this->page = (int) $this->request->get("page", 1);
        $this->managerKey = Api::managerPrimaryKey();
        $this->debugMode = (boolean) $this->request->get("debug", false);
    }

    /**
     * @param null $value
     * @return bool
     */
    public function debug($value = null)
    {
        if(!is_null($value)) $this->debugMode = (bool) $value;
        return $this->debugMode;
    }

    /**
     * @param array $data
     * @param string $message
     * @param int $status
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\JsonResponse
     */
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
        catch(Exception $exception){
        }
        return 0;
    }
}
