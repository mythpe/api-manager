<?php

namespace Myth\Api\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CollectionResponse extends ResourceCollection
{

    /**
     * The resource that this resource collects.
     * @var string
     */
    public $collects = "Myth\\Api\\Resources\\ResponseJsonResource";
    /** @var string */
    public $message = '';
    /** @var bool */
    public $success = true;

    /**
     * CollectionResponse constructor.
     * @param $resource
     * @param null $collects
     * @param string $message
     * @param bool $success
     */
    public function __construct($resource, $collects = null, string $message = "", bool $success = true)
    {
        parent::__construct($resource);
        $this->message = $message;
        $this->success = $success;
        $collects && ($this->collects = $collects);
    }

    /**
     * Transform the resource collection into an array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "data"    => $this->collection,
            "message" => (string) $this->message,
            "success" => (boolean) $this->success,
        ];
    }
}
