<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Wrappers;

trait ApiWrapperHelper
{

    /**
     * Helper
     * @param $model
     * @return string
     */
    public function modelToString($model): string
    {
        return (string) !is_string($model) ? get_class($model) : $model;
    }
}