<?php
namespace modele;

class ReservationGateway {
    private Connection $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    // Liste complète (ex: pour debug / page admin)
    public function getAll(): array {
        $sql = "SELECT idContrat, DateDebut, DateFin, Vehicule, Client, EtatDesLieu
                FROM Contrat
                ORDER BY DateDebut DESC";
        $this->connection->executeQuery($sql);
        return $this->connection->getResults();
    }

    // Réservations en cours aujourd'hui (inclusives)
    public function getCurrentReservation(): array {
        $sql = "SELECT idContrat, DateDebut, DateFin, Vehicule, Client, EtatDesLieu
                FROM Contrat
                WHERE CURDATE() BETWEEN DateDebut AND DateFin";
        $this->connection->executeQuery($sql);
        return $this->connection->getResults();
    }
    public function searchReservations(string $champ, string $q, string $filtre): array
{
    $where  = [];
    $params = [];

    // filtre de date
    switch ($filtre) {
        case 'a-venir':  $where[] = "DateDebut > CURDATE()"; break;
        case 'passees':  $where[] = "DateFin   < CURDATE()"; break;
        case 'toutes':   $where[] = "1";                    break;
        case 'en-cours':
        default:         $where[] = "CURDATE() BETWEEN DateDebut AND DateFin"; break;
    }

    // recherche texte
    if ($q !== '') {
        if ($champ === 'idContrat' || $champ === 'Client') {
            $where[] = "$champ = :qnum";
            $params[':qnum'] = (int)$q;
        } else { // Vehicule (VIN)
            $where[] = "$champ LIKE :q";
            $params[':q'] = "%{$q}%";
        }
    }

    $sql = "SELECT idContrat, Vehicule, DateDebut, DateFin, Client, EtatDesLieu
            FROM Contrat
            WHERE " . implode(' AND ', $where) . "
            ORDER BY DateDebut DESC";

    $this->connection->executeQuery($sql, $params);
    return $this->connection->getResults();
}

}
