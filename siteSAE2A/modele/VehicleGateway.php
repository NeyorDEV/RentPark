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
    
    

    public function getMostRentedCar(): ?array
    {
        $query = "
            SELECT 
                v.Marque,
                v.Nom AS Modele,
                v.ImagePath,
                COUNT(*) AS nb_locations
            FROM Contrat c
            JOIN Vehicule v ON c.IdVehicule = v.NumSerie
            WHERE c.Statut = 'Terminé'
            GROUP BY v.Marque, v.Nom
            ORDER BY nb_locations DESC
        ";
    
        $this->connection->executeQuery($query);
        $rows = $this->connection->getResults();
    
        if (empty($rows)) {
            return null;
        }
    
        return $rows[0]; // On renvoie seulement la voiture la plus louée
    }
    
    public function getRentableVehiculesBetweenDates(string $date_depart, string $date_retour): array
    {
    // On utilise la table 'cars' comme dans vos autres méthodes
    $query = "SELECT * FROM Vehicule v 
            WHERE v.NumSerie NOT IN (
                SELECT c.idVehicule 
                FROM Contrat c 
                WHERE NOT (c.DateFin< :date_depart OR c.DateDebut > :date_retour)
            )";

    $params = [
        ':date_depart' => [$date_depart, \PDO::PARAM_STR],
        ':date_retour' => [$date_retour, \PDO::PARAM_STR]
    ];

    $this->connection->executeQuery($query, $params);
    return $this->connection->getResults();
}
}
?>

