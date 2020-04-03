<?php

namespace Myth\Api\Interfaces;

class ResponseInterface
{

    protected $request;
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

    public function request(): \GuzzleHttp\Psr7\Response
    {
        return $this->request;
    }

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
        return (int) isset($this->response()['client_id']) ? $this->response()['client_id'] : 0;
    }
}