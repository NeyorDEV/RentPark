<?php
namespace modeleApi\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use modeleApi\Service\VehiculeService;
use modeleApi\Connection;

class VehiculeController {
    private $service;

    public function __construct(Connection $conn) {
        $this->service = new VehiculeService($conn);
    }

    public function getAll(Request $request, Response $response) {
        $data = $this->service->getAll($request->getQueryParams());
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getById(Request $request, Response $response, $args) {
        try {
            $data = $this->service->getById($args['numSerie']);
            $response->getBody()->write(json_encode($data));
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($e->getCode() ?: 500);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function add(Request $request, Response $response) {
        $data = $request->getParsedBody();
        try {
            $this->service->add($data);
            $response->getBody()->write(json_encode(['message' => 'Véhicule ajouté']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($e->getCode() ?: 500);
        }
    }

    public function update(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        try {
            $this->service->update($args['numSerie'], $data);
            $response->getBody()->write(json_encode(['message' => 'Véhicule mis à jour']));
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($e->getCode() ?: 500);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, $args) {
        try {
            $this->service->delete($args['numSerie']);
            $response->getBody()->write(json_encode(['message' => 'Véhicule supprimé']));
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($e->getCode() ?: 500);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}