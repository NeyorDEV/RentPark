<?php
namespace modeleApi\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use modeleApi\Service\ClientService;

class ClientController {

    private $service;

    public function __construct(ClientService $service) {
        $this->service = $service;
    }

    public function getAll(Request $request, Response $response) {

        $clients = $this->service->getAllClients();

        $response->getBody()->write(json_encode($clients));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function save(Request $request, Response $response) {

        $data = $request->getParsedBody();
        $result = $this->service->saveClient($data);

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }
}