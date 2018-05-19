<?php
/**
 * Created by PhpStorm.
 * User: Zdenda
 * Date: 18.5.2018
 * Time: 0:12
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware ktery zpracovava OPTION requesty.
 *
 * @package App\Http\Middleware
 */
class CorsMiddleware
{
    public function handle(Request $request, Closure $next )
    {
        if ($request->getMethod() === "OPTIONS") {
            $response = response('',200);
        } else {
            $response = $next($request);
        }

        $response->header('Access-Control-Allow-Methods', 'HEAD, GET, POST, PUT, PATCH, DELETE');
        $response->header('Access-Control-Allow-Headers', $request->header('Access-Control-Request-Headers'));
        $response->header('Access-Control-Allow-Origin', '*');
        return $response;
    }
}