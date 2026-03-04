<?php


namespace App\Middleware;
class RequestIdMiddleware
{
    public static function handle()
    {
        $requestId = bin2hex(random_bytes(8));
        $_SERVER['REQUEST_ID'] = $requestId;
        header("X-Request-Id: $requestId");
    }
}