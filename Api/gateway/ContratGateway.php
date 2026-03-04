<?php
namespace modeleApi\Gateway;

class ContratGateway {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAll() {
        $sql = "SELECT * FROM Contrat";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function insert($data) {

        $sql = "INSERT INTO Contrat 
                (DateDebut, DateFin, PrixTotal, idClient, idVehicule)
                VALUES (:dateDebut, :dateFin, :prixTotal, :idClient, :idVehicule)";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':dateDebut' => $data['DateDebut'],
            ':dateFin' => $data['DateFin'],
            ':prixTotal' => $data['PrixTotal'],
            ':idClient' => $data['idClient'],
            ':idVehicule' => $data['idVehicule']
        ]);

        return $this->conn->lastInsertId();
    }
}