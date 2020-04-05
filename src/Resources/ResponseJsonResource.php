<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ResponseJsonResource
 * @package Myth\Api\Resources
 */
class ResponseJsonResource extends JsonResource
{

    /**
     * Indicates if the resource's collection keys should be preserved.
     * @var bool
     */
    public $preserveKeys = true;

    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
