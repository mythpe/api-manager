<?php

namespace Myth\Api\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Myth\Api\Facades\Api;

class HttpClient
{

    /** @var string $authentication header authentication name */
    protected $tokenName = '';
    /** @var string $token header authentication value */
    protected $token = '';
    /** @var array $httpOptions Client options */
    private $httpOptions = [];
    /** @var \GuzzleHttp\Client $client */
    private $client;

    /**
     * HttpClient constructor.
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->setTokenName(Api::getTokenName());
        $this->createClient($options);
    }

    /**
     * @return string
     */
    public function getTokenName(): string
    {
        return $this->tokenName;
    }

    /**
     * @param string $tokenName
     * @return HttpClient
     */
    public function setTokenName(string $tokenName): HttpClient
    {
        $this->tokenName = $tokenName;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return HttpClient
     */
    public function setToken(string $token): HttpClient
    {
        $this->token = preg_replace('/\s+/', '', trim($token));
        if($this->getTokenName()){
            $this->setHeader($this->getTokenName(), $this->getToken());
        }
        return $this;
    }

    /**
     * set http option
     * @param string $key
     * @param mixed $value
     * @return HttpClient
     */
    public function setOption(string $key, $value): HttpClient
    {
        if(strtolower($key) === "headers"){
            foreach($value as $k => $v){
                $this->setHeader($k, $v);
            }
        }
        $this->httpOptions[$key] = $value;
        return $this;
    }

    /**
     * Get http option
     * @param string|null $key
     * @return array|mixed|null
     */
    public function getOption(string $key = null)
    {
        if(is_null($key)) return $this->httpOptions;
        return $this->httpOptions[strtolower($key)]??null;
    }

    /**
     * @param string $key
     * @param string $value
     * @return HttpClient
     */
    public function setHeader(string $key, string $value): HttpClient
    {
        $this->httpOptions['headers'][strtolower($key)] = $value;
        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getHeader(string $key = null)
    {
        if(is_null($key)) return $this->httpOptions['headers'];
        return $this->httpOptions['headers'][strtolower($key)]??null;
    }

    /**
     * @param null $key
     * @param null $value
     * @return mixed|null
     */
    public function header($key = null, $value = null)
    {
        if(is_null($key)) return $this->httpOptions['headers'];
        if(is_array($key)){
            foreach($key as $k => $v){
                $this->setHeader($k, $v);
            }
            return null;
        }
        elseif(is_null($value)) return $this->getHeader($key);
        else $this->setHeader($key, $value);
        return null;
    }

    /**
     * Set header manager name
     * @param string $name
     * @return HttpClient
     */
    public function setManagerName(string $name): HttpClient
    {
        $this->setHeader(Api::getManagerKeyName(), $name);
        return $this;
    }

    /**
     * Set header client token
     * @param string $token
     * @return HttpClient
     */
    public function setClientToken(string $token): HttpClient
    {
        $this->setToken($token);
        return $this;
    }

    /**
     * Wrapper of \GuzzleHttp\Client
     * @return Client
     */
    public function client() :Client
    {
        return $this->client;
    }

    public function request($method, $uri, $options = [])
    {
        $method = strtoupper($method);
        // return $this->client()->request($method, $uri);
        try{
            $response = $this->client()->request($method, $uri, $options);
            // d($response->getBody()->getContents());
            $data = json_decode($response->getBody()->getContents(), true);
            // d($data);
            // d($response->getStatusCode(),$response->getHeader('accepts'),$response->getBody());
            // d($response->getBody()->);
            // d($response->getBody()->getContents());
        }
        catch(RequestException $e){
            $data = [];
            // d($e);
            // d(\GuzzleHttp\Psr7\str($e->getResponse()));
            // d(\GuzzleHttp\Psr7\str($e->getRequest()));
        }
        $status = $response->getStatusCode();
        // d($data);
        $res = array_merge(is_array($data) ? $data : [], [
                'success' => (boolean) (isset($data['success']) ? $data['success'] : $status == 200),
                'status'  => (int) $status,
                'message' => (string) (isset($data['message']) ? $data['message'] : ''),
                'data'    => (array) (isset($data['data']) ? $data['data'] : []),
                'errors'  => (array) (isset($data['errors']) ? $data['errors'] : []),
            ]);
        krsort($res);
        return $res;
    }

    /**
     * Set Default http client headers option
     * @return $this
     */
    protected function setDefaultHeaders(): HttpClient
    {
        /**
         * Set http headers options array
         */
        if(!array_key_exists('headers', $this->httpOptions)){
            $this->httpOptions['headers'] = [];
        }
        /**
         * Set API header authentication
         */
        if($this->getTokenName() && $this->getToken()){
            $this->header($this->getTokenName(), $this->getToken());
        }
        /**
         * Set Default options.
         */
        $this->header([
            'X-REQUEST-WITH' => "MyTh GuzzleHttp v1.1",
            'Accept'         => "application/json",
            'Content-Type'   => "application/json",
        ]);
        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    protected function createClient($options = []): HttpClient
    {
        $this->setOptions($options);
        $this->client = new Client($this->httpOptions);
        return $this;
    }

    /**
     * set Http options
     * @param array $options
     * @return HttpClient
     */
    protected function setOptions($options = []): HttpClient
    {
        $this->setOption('http_errors', false);
        if(is_string($options)){
            $this->setOption('base_uri', $options);
        }
        else{
            foreach($options as $k => $v){
                $this->setOption($k, $v);
            }
        }
        $this->setDefaultHeaders();
        return $this;
    }

}