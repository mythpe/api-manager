<?php

namespace Myth\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
        // $resource = $this->resource;
        // $resource = $resource->appendRelations($resource->appendToMythApiArray());
        return $this->resource;
    }
}
