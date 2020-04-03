<?php

namespace Myth\Api\Wrappers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;

trait ResponseTrait
{

    /**
     * Return static json response
     * @param array $data
     * @param string $message
     * @param int $status
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function jsonResponse(
        $data = [], $message = '', $status = 200, array $headers = [], $options = 0
    ): JsonResponse {
        $success = null;
        if(func_num_args() === 1 && is_array($data)){
            if(isset($data['message'])){
                $message = $data['message'];
                unset($data['message']);
            }
            if(isset($data['success'])){
                $success = $data['success'];
                unset($data['success']);
            }
            if(isset($data['status'])){
                $status = $data['status'];
                unset($data['status']);
            }
            if(isset($data['options'])){
                $options = $data['options'];
                unset($data['options']);
            }
            if(isset($data['headers'])){
                $headers = $data['headers'];
                unset($data['headers']);
            }
            if(isset($data['data'])){
                $data = $data['data'];
                unset($data['data']);
            }
        }

        $json = [
            "message" => (string) $message,
            "success" => (boolean) (is_null($success) ? ((int) $status === 200) : $success),
            "data"    => $data,
        ];

        return $this->response()->json($json, $status, $headers, $options)->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    /**
     * Return a new response from the application.
     * @param \Illuminate\View\View|string|array|null $content
     * @param int $status
     * @param array $headers
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function response($content = '', $status = 200, array $headers = [])
    {
        $factory = app(ResponseFactory::class);

        if(func_num_args() === 0){
            return $factory;
        }

        return $factory->make($content, $status, $headers);
    }
}