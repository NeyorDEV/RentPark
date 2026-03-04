<?php
namespace modeleApi\Controller;

use modeleApi\Service\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController {

    private UserService $service;

    public function __construct(UserService $service) {
        $this->service = $service;
    }

    public function create(Request $request, Response $response) {
        $data = (array)$request->getParsedBody();
        $user = $this->service->createUser($data);
        $response->getBody()->write(json_encode($user));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getAll(Request $request, Response $response) {
        $users = $this->service->getAllUsers();
        $response->getBody()->write(json_encode($users));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getById(Request $request, Response $response, array $args) {
        $user = $this->service->getUserById($args['id']);
        $response->getBody()->write(json_encode($user));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args) {
        $data = (array)$request->getParsedBody();
        $user = $this->service->updateUser($args['id'], $data);
        $response->getBody()->write(json_encode($user));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, array $args) {
        $this->service->deleteUser($args['id']);
        $response->getBody()->write(json_encode(['status' => 'deleted']));
        return $response->withHeader('Content-Type', 'application/json');
    }
}