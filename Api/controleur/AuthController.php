<?php
namespace modeleApi\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use modeleApi\Service\AuthService;

class AuthController {

    private $service;

    public function __construct(AuthService $service) {
        $this->service = $service;
    }

    public function login(Request $request, Response $response) {

        $data = $request->getParsedBody();
        $result = $this->service->login($data['Email'], $data['Password']);

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }
}