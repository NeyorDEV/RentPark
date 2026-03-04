<?php

namespace App\Middleware;
class JsonResponse
{
    public static function success($data, int $status = 200)
    {
        http_response_code($status);
        echo json_encode([
            "success" => true,
            "requestId" => $_SERVER['REQUEST_ID'] ?? null,
            "data" => $data
        ]);
        exit;
    }

    public static function error(string $message, int $status = 400)
    {
        http_response_code($status);
        echo json_encode([
            "success" => false,
            "requestId" => $_SERVER['REQUEST_ID'] ?? null,
            "error" => $message
        ]);
        exit;
    }
}