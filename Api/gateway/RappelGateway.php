<?php
namespace modeleApi\Gateway;

class RappelGateway {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAll() {
        $this->conn->executeQuery("SELECT * FROM Rappel");
        return $this->conn->getResults();
    }
}