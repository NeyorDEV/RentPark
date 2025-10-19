<?php
namespace modele;
class ReservationGateway {
    private Connection $connection;
    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }
    public function getAll(): array {
        $this->connection->executeQuery("SELECT idContrat,dateDebut FROM Contrat ORDER BY DESC");
        return $this->connection->getResults();
    }
    public function getCurrentReservation() : array {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

        $sql = "SELECT *
                FROM Contrat
                WHERE :now BETWEEN DateDebut AND DateFin";

        $this->connection->executeQuery($sql, ['now' => $now->format('Y-m-d H:i:s')]);

        return $this->connection->getResults();
    }
}
?>
