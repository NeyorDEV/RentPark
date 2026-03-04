<?php
namespace modeleApi\Service;

use modeleApi\Gateway\ContratGateway;

class ContratService {

    private $gateway;

    public function __construct(ContratGateway $gateway) {
        $this->gateway = $gateway;
    }

    public function getAll() {
        return $this->gateway->getAll();
    }

    public function create($data) {

        if (!isset($data['DateDebut'], $data['DateFin'], $data['PrixTotal'])) {
            return ['error' => 'Données manquantes'];
        }

        $id = $this->gateway->insert($data);

        return [
            'message' => 'Contrat créé',
            'idContrat' => $id
        ];
    }
}