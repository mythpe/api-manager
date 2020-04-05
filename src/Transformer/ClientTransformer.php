<?php

namespace Myth\Api\Transformer;

abstract class ClientTransformer extends MagicTransformer
{

    /**
     * The body of  the http request will we append when sync new data to client
     * @return array
     */
    abstract public function body(): array;
}