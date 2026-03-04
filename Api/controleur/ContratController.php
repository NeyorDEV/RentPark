<?php
namespace modeleApi\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use modeleApi\Service\ContratService;

class ContratController {

    private $service;

    public function __construct(ContratService $service) {
        $this->service = $service;
    }

    public function getAll(Request $request, Response $response) {

        $result = $this->service->getAll();
        $response->getBody()->write(json_encode($result));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response) {

        $data = $request->getParsedBody();
        $result = $this->service->create($data);

        $response->getBody()->write(json_encode($result));

        return $response->withHeader('Content-Type', 'application/json');
    }
}