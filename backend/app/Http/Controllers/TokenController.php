<?php
/**
 * Created by PhpStorm.
 * User: Zdenda
 * Date: 29.4.2018
 * Time: 10:20
 */

namespace App\Http\Controllers;

use \Firebase\JWT\JWT;

class TokenController extends Controller
{
    /**
     * Vygeneruje nový token pro JWT autorizaci.
     */
    public function generateToken() {
        $duration = env('JWT_DURATION', 1800);

        // parametry jwt
        $iat = time();
        $exp = $iat + $duration;
        $iss = env('JWT_ISS', 'aswi-doprava');

        // generovani tokenu
        $key = env('JWT_SECRET', '');
        $token = array(
            'iss' => $iss,
            'iat' => $iat,
            'exp' => $exp
        );

        $jwt = JWT::encode($token, $key);

        return $jwt;
    }
}