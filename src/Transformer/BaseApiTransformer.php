<?php

namespace Myth\Api\Transformer;

abstract class BaseApiTransformer
{

    /** @var mixed $model locale model object */
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * The body of  the http request will we append when sync new data to client
     * @return array
     */
    abstract public function body(): array;

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
        return $this->model->{$name} = $value;
    }
}