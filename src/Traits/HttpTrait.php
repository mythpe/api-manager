<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Traits;

use GuzzleHttp\Client;

class HttpTrait
{

    /** @var string $authentication header authentication name */
    protected $authenticationName;
    /** @var string $authenticationValue header authentication value */
    protected $authenticationValue;
    /** @var array $httpOptions Client options */
    private $httpOptions = [];
    /** @var \GuzzleHttp\Client $httpClient */
    private $httpClient;

    /**
     * Set Default http client headers option
     * @return $this
     */
    protected function setDefaultHeaders()
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
        if($this->authenticationName){
            $this->httpOptions['headers'][$this->authenticationName] = $this->authenticationValue;
        }
        /**
         * Set Default options.
         */
        $this->httpOptions['headers']['X-REQUEST-WITH'] = "MyTh GuzzleHttp v1.1";
        $this->httpOptions['headers']['Accept'] = 'application/json';
        $this->httpOptions['headers']['Content-Type'] = 'application/json';
        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    protected function createClient($options = [])
    {
        $this->setOptions($options);
        $this->httpClient = new Client($this->httpOptions);
        return $this;
    }

    /**
     * @param array $options
     */
    protected function setOptions($options = [])
    {
        $this->httpOptions['http_errors'] = false;
        if(is_string($options)){
            $this->httpOptions['base_uri'] = $options;
        }
        else{
            $this->httpOptions = array_merge($this->httpOptions, $options);
        }
        $this->setDefaultHeaders();
    }
}