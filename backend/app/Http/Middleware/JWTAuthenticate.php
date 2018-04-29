<?php
/**
 * Created by PhpStorm.
 * User: Zdenda
 * Date: 29.4.2018
 * Time: 11:26
 */

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

/**
 * Middleware slouzici ke kontrole JWT v hlavicce requestu (pokud je JWT overovani zapnute).
 *
 * @package App\Http\Middleware
 */
class JWTAuthenticate
{
    public function handle(Request $request, Closure $next) {
        // pokud je JWT overovani vypnute, nedelej nic
        if(!env('JWT_AUTH_ENABLED')) {
            return $next($request);
        }

        $jwtHeaderName = env('JWT_HEADER_NAME', 'jwt');
        $jwt = $request->header($jwtHeaderName);
        if ($jwt == null) {
            // token chybi
            return response('Unauthorized.', 401);
        }

        $key = env('JWT_SECRET', '');
        $decoded = null;
        try {
            $decoded = JWT::decode($jwt, $key, array('HS256'));
        } catch (\Exception $ex ) {
            // jakakoliv chyba pri dekodovani (i expirace) => 401
            return response('Unauthorized.', 401);
        }

        // kontrola, ze token nebyl odcizen: musi sedet ip a prohlizec odesilatele
        $decoded_array = (array) $decoded;

        if($this->checkJwt($request, $decoded_array)) {
            return $next($request);
        } else {
            return response('Unauthorized.', 401);
        }
    }

    /**
     * Zkontroluje jwt rozkodovany do pole proti hodnotam z requestu. Pokud hodnoty nesedi vrati false.
     * Expirovani tokenu zde neni kontrolovano - melo by byt uz pri dekodovani tokenu.
     *
     * @param Request $request Request.
     * @param array $jwt JWT rozkodovany do pole.
     * @return True pokud je JWT v poradku, false pokud ne.
     */
    private function checkJwt(Request $request, array $jwt) {
        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');
        if (!array_key_exists('ipaddr', $jwt) || ! array_key_exists('user-agent', $jwt)) {
            return false;
        }
        if($ip != $jwt['ipaddr'] || $userAgent != $jwt['user-agent']) {
            return false;
        }

        return true;
    }
}