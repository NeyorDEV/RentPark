<?php
namespace modele;

class ReservationGateway {
    private Connection $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }
    public function searchReservations(string $champ, string $q, string $filtre): array {
        $where  = [];
        $params = [];

        // filtre date
        switch ($filtre) {
            case 'a-venir': $where[] = "DateDebut > CURDATE()"; break;
            case 'passees': $where[] = "DateFin   < CURDATE()"; break;
            case 'toutes':  $where[] = "1";                     break;
            default:        $where[] = "CURDATE() BETWEEN DateDebut AND DateFin";
        }

        // recherche
        if ($q !== '') { // id de contrat ou de client
            if ($champ === 'idContrat' || $champ === 'Client') {
                $where[] = "$champ = :qnum";
                $params[':qnum'] = [ (int)$q, \PDO::PARAM_INT ];
            } else { // Vehicule (VIN)
                $where[] = "$champ LIKE :q";
                $params[':q'] = [ "%{$q}%", \PDO::PARAM_STR ];
            }
        }

        $sql = "SELECT idContrat, idVehicule, DateDebut, DateFin, IdClient, EtatAvant
                FROM Contrat
                WHERE ".implode(' AND ', $where)."
                ORDER BY DateDebut DESC";

        $this->connection->executeQuery($sql, $params);
        return $this->connection->getResults();
    }
    public function insertReservation(string $vehicule, int $client, string $dateDebut, string $dateFin): void {
        $sql = "INSERT INTO Contrat (DateDebut, DateFin, Vehicule, Client, EtatDesLieu)
                VALUES (:d1, :d2, :veh, :cli, :etat)";
        $this->connection->executeQuery($sql, [
            ':d1'   => [$dateDebut, \PDO::PARAM_STR],
            ':d2'   => [$dateFin,   \PDO::PARAM_STR],
            ':veh'  => [$vehicule,  \PDO::PARAM_STR],
            ':cli'  => [$client,    \PDO::PARAM_INT],
            ':etat' => ['neuf',     \PDO::PARAM_STR], 
        ]);
    }
    public function update(int $id, string $vehicule, int $client, string $dateDebut, string $dateFin): void {
        $sql = "UPDATE Contrat
                SET DateDebut = :d1, DateFin = :d2, Vehicule = :veh, Client = :cli
                WHERE idContrat = :id";
        $params = [
            ':d1'  => [$dateDebut, \PDO::PARAM_STR],
            ':d2'  => [$dateFin,   \PDO::PARAM_STR],
            ':veh' => [$vehicule,  \PDO::PARAM_STR],
            ':cli' => [$client,    \PDO::PARAM_INT],
            ':id'  => [$id,        \PDO::PARAM_INT],
        ];
        $this->connection->executeQuery($sql, $params);
    }

    public function delete(int $id): void {
        $query = "DELETE FROM Contrat WHERE idcontrat = :id";
        $params = [
            ':id' => [$id, \PDO::PARAM_INT]
        ];
        $this->connection->executeQuery($query, $params);
    }

    public function getMonthlyIncome(): float {

        $query = "
            SELECT SUM(m.Prix) AS total
            FROM Contrat c
            JOIN Vehicule v ON c.idVehicule = v.numSerie
            JOIN Modele m 
                ON m.Marque = v.Marque
               AND m.Nom = v.Nom
               AND m.Annee = v.Annee
            WHERE MONTH(c.DateDebut) = MONTH(CURRENT_DATE())
              AND YEAR(c.DateDebut) = YEAR(CURRENT_DATE());
        ";
    
        $this->connection->executeQuery($query);
        $results = $this->connection->getResults();
    
        if (empty($results) || $results[0]["total"] === null) {
            return 0.0;
        }
    
        return (float)$results[0]["total"];
    }

    public function getContractsForNextMonth(): array
{
    $query = "
        SELECT 
            c.DateDebut,
            c.DateFin,
            v.Marque,
            v.Nom AS Modele
        FROM Contrat c
        JOIN Vehicule v ON c.IdVehicule = v.NumSerie
        WHERE (c.DateDebut BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 MONTH))
           OR (c.DateFin   BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 MONTH))
        ORDER BY c.DateDebut ASC
    ";

    $this->connection->executeQuery($query);
    $results = $this->connection->getResults();

    // Renvoie un tableau vide si rien trouvé
    if (empty($results)) {
        return [];
    }

    return $results;
}

    
    
}
