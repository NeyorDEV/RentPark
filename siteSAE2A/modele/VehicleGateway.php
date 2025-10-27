<?php
namespace modele;
class VehicleGateway {
    private Connection $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public function getAll(): array {
        $this->connection->executeQuery("SELECT * FROM cars");
        return $this->connection->getResults();
    }

    public function add(string $modele, string $couleur, string $puissance, string $imagePath = ''): void {
        $query = "INSERT INTO cars (modele, couleur, puissance, image_path) 
                  VALUES (:modele, :couleur, :puissance, :image_path)";
        $params = [
            ':modele'     => [$modele, \PDO::PARAM_STR],
            ':couleur'    => [$couleur, \PDO::PARAM_STR],
            ':puissance'  => [$puissance, \PDO::PARAM_STR],
            ':image_path' => [$imagePath, \PDO::PARAM_STR],
        ];
        $this->connection->executeQuery($query, $params);
    }
    

    public function delete(int $id): void {
        $query = "DELETE FROM cars WHERE voiture = :id";
        $params = [
            ':id' => [$id, \PDO::PARAM_INT]
        ];
        $this->connection->executeQuery($query, $params);
    }

    public function update(int $id, string $modele, string $couleur, string $puissance): void {
        $query = "UPDATE cars SET modele = :modele, couleur = :couleur, puissance = :puissance WHERE voiture = :id";
        $params = [
            ':modele'   => [$modele, \PDO::PARAM_STR],
            ':couleur'  => [$couleur, \PDO::PARAM_STR],
            ':puissance'=> [$puissance, \PDO::PARAM_STR],
            ':id'       => [$id, \PDO::PARAM_INT],
        ];
        $this->connection->executeQuery($query, $params);
    }

    public function rechercherVoitures(string $motCle): array {
        $query = "SELECT * FROM cars WHERE modele LIKE :q";
        $params = [
            ':q' => ["%$motCle%", \PDO::PARAM_STR]
        ];
    
        $this->connection->executeQuery($query, $params);
    
       
        return $this->connection->getResults();
    }   



}
?>

