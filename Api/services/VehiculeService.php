<?php
namespace modeleApi\Service;

use modeleApi\Gateway\VehiculeGateway;

class VehiculeService {
    private $gateway;

    public function __construct($conn) {
        $this->gateway = new VehiculeGateway($conn);
    }

    public function getAll($filters = []) {
        return $this->gateway->getAll($filters);
    }

    public function getById($numSerie) {
        $vehicule = $this->gateway->getById($numSerie);
        if (!$vehicule) {
            throw new \Exception("Véhicule non trouvé", 404);
        }
        return $vehicule;
    }

    public function add($data) {
        //  on peut rajouter conditions pour les champs obligatoires
        if (empty($data['NumSerie'])) {
            throw new \Exception("NumSerie obligatoire", 400);
        }

        $this->gateway->insert($data);
    }

    public function update($numSerie, $data) {
        $vehicule = $this->gateway->getById($numSerie);
        if (!$vehicule) {
            throw new \Exception("Véhicule non trouvé", 404);
        }
        $this->gateway->update($numSerie, $data);
    }

    public function delete($numSerie) {
        $vehicule = $this->gateway->getById($numSerie);
        if (!$vehicule) {
            throw new \Exception("Véhicule non trouvé", 404);
        }
        $this->gateway->delete($numSerie);
    }
}