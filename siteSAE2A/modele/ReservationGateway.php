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
        if ($q !== '') {
            if ($champ === 'idContrat' || $champ === 'Client') {
                $where[] = "$champ = :qnum";
                $params[':qnum'] = [ (int)$q, \PDO::PARAM_INT ];
            } else { // Vehicule (VIN)
                $where[] = "$champ LIKE :q";
                $params[':q'] = [ "%{$q}%", \PDO::PARAM_STR ];
            }
        }

        $sql = "SELECT idContrat, Vehicule, DateDebut, DateFin, Client, EtatDesLieu
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
            ':etat' => ['neuf',     \PDO::PARAM_STR], // adapte à ton schéma (NOT NULL ? valeur par défaut ?)
        ]);
    }
    public function delete(int $id): void {
        $query = "DELETE FROM Contrat WHERE idcontrat = :id";
        $params = [
            ':id' => [$id, \PDO::PARAM_INT]
        ];
        $this->connection->executeQuery($query, $params);
    }
}
