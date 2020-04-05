<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Myth\Api\Facades\Api;

/**
 * Class AuthenticateMiddleware
 * @package Myth\Api\Middlewares
 */
class AuthenticateMiddleware
{

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');
        if(($manager = Api::resolveRouteManagerConnection($request))){
            $request->setAuthManager($manager);
        }
        return $next($request);
    }
}
