<?php
namespace App\Security;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;
use App\Middleware\JsonResponse; // Doit correspondre au dossier Middleware
use Exception;

class AuthMiddleware
{
    public function __invoke(Request $request, Handler $handler): Response
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader) {
            return JsonResponse::error("Token manquant", 401)->withStatus(401);
        }

        $token = str_replace("Bearer ", "", $authHeader);

        try {
            
            $decoded = JwtService::decode($token); 
            $_SERVER['USER'] = (array) $decoded;

            return $handler->handle($request);
        } catch (Exception $e) {
            return JsonResponse::error("Token invalide : " . $e->getMessage(), 401)->withStatus(401);
        }
    }
}