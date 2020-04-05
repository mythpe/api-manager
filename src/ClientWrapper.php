<?php

namespace Myth\Api;

class ClientWrapper
{

    /** @var string $name manager name */
    protected $name;
    /** @var array $config manager config */
    protected $config;
    /** @var array[] $models list of locale models must will be sync with manager */
    protected $models = [];
    /** @var array[] $options manager options */
    protected $options = [];

    public function __construct($config, $name)
    {
        /** fill client config */
        $this->fillClientConfig($config, $name);
        // d($this, $config, $name);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array[]
     */
    public function getModels(): array
    {
        return $this->models;
    }

    /**
     * Get new model wrapper of manger
     * @param $model
     * @return ManagerModelWrapper
     */
    public function model($model): ManagerModelWrapper
    {
        return new ManagerModelWrapper($this->getName(), $model);
    }

    /**
     * manager has model
     * @param string $model
     * @return bool
     */
    public function hasModel(string $model): bool
    {
        return array_key_exists($model, $this->models);
    }

    /**
     * manager has model from route bind
     * @param string $route
     * @return bool
     */
    public function hasModelFromRoute(string $route): bool
    {
        return !is_null($this->getModelFromRoute($route));
    }

    /**
     * Get manager model from route binding
     * @param string $route
     * @return null|string
     */
    public function getModelFromRoute(string $route)
    {
        foreach($this->getModels() as $model => $config){
            if($config['uri'] === $route) return (string) $model;
        }
        return null;
    }

    /**
     * @return array[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array[] $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @param null $key
     * @return array|array[]|mixed
     */
    public function getOption($key = null)
    {
        if(is_null($key)) return $this->getOptions();
        return $this->options[$key];
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setOption(string $key, $value): void
    {
        $this->options[$key] = $value;
    }

    /**
     * @param null $key
     * @param null $value
     * @return $this|array|array[]|mixed
     */
    public function option($key = null, $value = null)
    {
        if(is_null($key)) return $this->getOptions();
        if(is_null($value)) return $this->getOption($key);
        $this->setOption($key, $value);
        return $this;
    }

    /**
     * get specific locale model config of manager
     * @param $model
     * @return array
     */
    public function getModelConfig($model): array
    {
        $k = !is_string($model) ? get_class($model) : $model;
        return $this->models[$k];
    }

    /**
     * Fill client config data into class
     * @param $config
     * @param $name
     */
    protected function fillClientConfig($config, $name): void
    {
        $this->config = $config;
        $this->name = $name;
        $this->models = $this->config['models'];
        $this->options = $this->config['options'];
    }
}