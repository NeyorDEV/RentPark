<?php

namespace App\Security;
use App\Middleware\JsonResponse; 

class RoleMiddleware
{
    public static function requireRole(string $role)
    {
        $user = $_SERVER['USER'] ?? null;

        if (!$user || $user['role'] !== $role) {
            JsonResponse::error("Accès interdit", 403);
            exit;
        }
    }
}