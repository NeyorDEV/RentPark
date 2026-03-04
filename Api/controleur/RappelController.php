<?php
namespace modeleApi\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use modeleApi\Service\RappelService;

class RappelController {

    private $service;

    public function __construct(RappelService $service) {
        $this->service = $service;
    }

    public function getAll(Request $request, Response $response) {
        $data = $this->service->getAll();
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}