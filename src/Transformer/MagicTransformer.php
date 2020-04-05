<?php

namespace Myth\Api\Transformer;

/**
 * Class MagicTransformer
 * @package Myth\Api\Transformer
 */
class MagicTransformer
{

    /** @var mixed $model locale model object */
    private $model;

    /**
     * MagicTransformer constructor.
     * @param $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * Get an instance of the current request or an input item from the request.
     * @param mixed ...$args
     * @return \Illuminate\Http\Request|string|array
     */
    public function request(...$args)
    {
        return request(...$args);
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public function set($name, $value)
    {
        return $this->{$name} = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->{$name};
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->model->{$name}(...$arguments);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->model->{$name};
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->model->setAttribute($name, $value);
    }
}