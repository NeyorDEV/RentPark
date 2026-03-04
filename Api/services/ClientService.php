<?php
namespace modeleApi\Service;

use modeleApi\Gateway\ClientGateway;

class ClientService {

    private $gateway;

    public function __construct(ClientGateway $gateway) {
        $this->gateway = $gateway;
    }

    public function getAllClients() {
        return $this->gateway->getAll();
    }

    public function saveClient($data) {

        $existing = $this->gateway
                         ->findByEmailOrPermis($data['Email'], $data['NumPermis']);

        if ($existing) {
            $this->gateway->update($existing['idClient'], $data);
            return [
                'message' => 'Client mis à jour',
                'idClient' => $existing['idClient']
            ];
        }

        $id = $this->gateway->insert($data);

        return [
            'message' => 'Client créé',
            'idClient' => $id
        ];
    }
}