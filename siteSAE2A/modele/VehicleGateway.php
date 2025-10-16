<?php
namespace modele;
class VehicleGateway {
    private Connection $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public function getAll(): array {
        $this->connection->executeQuery("SELECT * FROM testphp");
        return $this->connection->getResults();
    }

    public function add(int $voiture, string $modele, string $couleur , string $puissance): void {
        $query = "INSERT INTO testphp VALUES (:voiture, :modele,:couleur,:puissance)";
        $params = [
            ':voiture' => [$voiture, \PDO::PARAM_INT],
            ':modele' => [$modele, \PDO::PARAM_STR],
            ':couleur' => [$couleur, \PDO::PARAM_STR],
            ':puissance' => [$puissance, \PDO::PARAM_STR],
        ];

        $this->connection->executeQuery($query, $params);
    }

    public function delete(int $id): void {
        $query = "DELETE FROM testphp WHERE voiture = :id";
        $params = [
            ':id' => [$id, \PDO::PARAM_INT]
        ];
        $this->connection->executeQuery($query, $params);
    }

    public function rechercherVoitures(string $motCle): array {
        $query = "SELECT * FROM testphp WHERE modele LIKE :q";
        $params = [
            ':q' => ["%$motCle%", \PDO::PARAM_STR]
        ];
    
        $this->connection->executeQuery($query, $params);
    
       
        return $this->connection->getResults();
    }
    
    
}
?>

