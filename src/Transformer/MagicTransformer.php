<?php

namespace Myth\Api\Transformer;

class MagicTransformer
{

    /** @var mixed $model locale model object */
    private $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

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

    public function set($name, $value)
    {
        return $this->{$name} = $value;
    }

    public function get($name)
    {
        return $this->{$name};
    }

    public function __call($name, $arguments)
    {
        return $this->model->{$name}(...$arguments);
    }

    public function __get($name)
    {
        return $this->model->{$name};
    }

    public function __set($name, $value)
    {
        $this->model->setAttribute($name, $value);
    }
}