<?php

namespace Myth\Api\Transformer;

use Illuminate\Http\Request;

abstract class ManagerTransformer
{

    /**
     * array will fill the model when manager sync new model into client
     * @return array
     * @uses @function setRawAttributes
     */
    abstract static function fillable($model, Request $request): array;

    /**
     * Transform model to array
     * @return array
     */
    abstract static function toArray($model, Request $request): array;

    /**
     * validate manager request
     * this function must return value of request validation
     * @param \Illuminate\Http\Request $request
     * @return bool|string error message
     */
    abstract static function validate(Request $request, $model);

    /**
     * Event model will saving in client database
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    abstract static function saving($model, Request $request): void;

    /**
     * Event model was saved in client database
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    abstract static function saved($model, Request $request): void;
}