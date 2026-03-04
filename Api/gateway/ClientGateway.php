<?php

namespace modeleApi\Gateway;

class ClientGateway {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAll() {
        $this->conn->executeQuery("SELECT * FROM Client");
        return $this->conn->getResults();
    }

    public function findByEmailOrPermis($email, $numPermis) {
        $sql = "SELECT idClient FROM Client 
                WHERE Email = :email OR NumPermis = :numPermis LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':numPermis' => $numPermis
        ]);

        return $stmt->fetch();
    }

    public function insert($data) {
        $sql = "INSERT INTO Client (Nom, Prenom, Email, NumTel, NumPermis, DateNaiss, Nationalite) 
                VALUES (:nom, :prenom, :email, :numTel, :numPermis, :dateNaiss, :nationalite)";

        $this->conn->executeQuery($sql, [
            ':nom' => [$data['Nom'], \PDO::PARAM_STR],
            ':prenom' => [$data['Prenom'], \PDO::PARAM_STR],
            ':email' => [$data['Email'], \PDO::PARAM_STR],
            ':numTel' => [$data['NumTel'] ?? null, \PDO::PARAM_STR],
            ':numPermis' => [$data['NumPermis'], \PDO::PARAM_STR],
            ':dateNaiss' => [$data['DateNaiss'] ?? null, \PDO::PARAM_STR],
            ':nationalite' => [$data['Nationalite'] ?? null, \PDO::PARAM_STR]
        ]);

        return $this->conn->getLastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE Client SET 
                Nom = :nom,
                Prenom = :prenom,
                Email = :email,
                NumTel = :numTel,
                NumPermis = :numPermis,
                DateNaiss = :dateNaiss,
                Nationalite = :nationalite
                WHERE idClient = :id";

        $this->conn->executeQuery($sql, [
            ':nom' => [$data['Nom'], \PDO::PARAM_STR],
            ':prenom' => [$data['Prenom'], \PDO::PARAM_STR],
            ':email' => [$data['Email'], \PDO::PARAM_STR],
            ':numTel' => [$data['NumTel'] ?? null, \PDO::PARAM_STR],
            ':numPermis' => [$data['NumPermis'], \PDO::PARAM_STR],
            ':dateNaiss' => [$data['DateNaiss'] ?? null, \PDO::PARAM_STR],
            ':nationalite' => [$data['Nationalite'] ?? null, \PDO::PARAM_STR],
            ':id' => [$id, \PDO::PARAM_INT]
        ]);
    }
}