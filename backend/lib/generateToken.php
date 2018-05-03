<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__ . '/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

use Illuminate\Http\Request;
use \Firebase\JWT\JWT;

function generateToken() {
    $request = Request::createFromGlobals();
    // parametry jwt
    $key = env('JWT_SECRET', '');
    $iss = env('JWT_ISS', 'aswi-doprava');

    $duration = env('JWT_DURATION', 1800);
    $iat = time();
    $exp = $iat + $duration;

    // generovani tokenu
    $token = array(
        'iss' => $iss,
        'iat' => $iat,
        'exp' => $exp,
        'ipaddr' => $request->ip(),
        'user-agent' => $request->header('User-Agent')
    );

    $jwt = JWT::encode($token, $key, 'HS256');

    return $jwt;
}