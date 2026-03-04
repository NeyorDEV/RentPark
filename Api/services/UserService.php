<?php
namespace modeleApi\Service;

use modeleApi\Gateway\UserGateway;

class UserService {
    private UserGateway $gateway;

    public function __construct(UserGateway $gateway) {
        $this->gateway = $gateway;
    }

    public function createUser(array $data) {
        return $this->gateway->insert($data);
    }

    public function getAllUsers() {
        return $this->gateway->findAll();
    }

    public function getUserById($id) {
        return $this->gateway->findById($id);
    }

    public function updateUser($id, array $data) {
        return $this->gateway->update($id, $data);
    }

    public function deleteUser($id) {
        return $this->gateway->delete($id);
    }
}