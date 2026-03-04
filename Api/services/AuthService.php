<?php
namespace modeleApi\Service;

use modeleApi\Gateway\UserGateway;


class AuthService {

    private $gateway;

    public function __construct(UserGateway $gateway) {
        $this->gateway = $gateway;
    }

    public function login($email, $password) {

        $user = $this->gateway->findByEmail($email);

        if (!$user || !password_verify($password, $user['Password'])) {
            return ['error' => 'Identifiants invalides'];
        }

        return [
            'message' => 'Connexion réussie',
            'user' => [
                'idUser' => $user['idUser'],
                'Nom' => $user['Nom'],
                'Email' => $user['Email']
            ]
        ];
    }
}