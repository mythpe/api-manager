<?php

namespace Myth\Api\Interfaces;

use Exception;
use GuzzleHttp\Psr7\Response;
use Myth\Api\Facades\Api;

/**
 * Class ResponseInterface
 * @package Myth\Api\Interfaces
 */
class ResponseInterface
{

    /** @var \GuzzleHttp\Psr7\Response */
    protected $request;

    /** @var array */
    protected $response;

    /**
     * ResponseInterface constructor.
     * @param \GuzzleHttp\Psr7\Response $request
     * @param array $response
     */
    public function __construct($request, array $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @return \GuzzleHttp\Psr7\Response
     */
    public function request(): Response
    {
        return $this->request;
    }

    /**
     * @return array
     */
    public function response(): array
    {
        return $this->response;
    }

    /***
     * get client id after sync from response
     * @return int
     */
    public function client_id(): int
    {
        $response = $this->response();
        try{
            return (int) $response['data'][Api::clientResponseKey()][Api::clientPrimaryKey()];
        }
        catch(Exception $exception){
        }
        return 0;
    }
}