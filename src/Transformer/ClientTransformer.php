<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Transformer;

/**
 * Class ClientTransformer
 * @package Myth\Api\Transformer
 */
abstract class ClientTransformer extends MagicTransformer
{

    /**
     * The body of  the http request will we append when sync new data to client
     * @return array
     */
    abstract public function body(): array;
}