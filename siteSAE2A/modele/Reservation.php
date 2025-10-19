<?php

namespace modele;

class Reservation
{
    private int $idContrat;
    private \DateTime $dateDebut;
    private \DateTime $dateFin;

    private string $vehiculeVin;     // Véhicule.numSérie (VIN) — alphanumérique
    private int $clientId;           // Client.idClient
    private int $etatDesLieuxId;     // EtatDesLieux.idEtatDesLieux

    public function __construct(
        int $idContrat,
        \DateTime $dateDebut,
        \DateTime $dateFin,
        string $vehiculeVin,
        int $clientId,
        int $etatDesLieuxId
    ) {
        $this->idContrat = $idContrat;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->vehiculeVin = $vehiculeVin;
        $this->clientId = $clientId;
        $this->etatDesLieuxId = $etatDesLieuxId;
    }

    public function getIdContrat(): ?int { return $this->idContrat; }
    public function getDateDebut(): \DateTime { return $this->dateDebut; }
    public function getDateFin(): \DateTime { return $this->dateFin; }

    public function getVehiculeVin(): string { return $this->vehiculeVin; }
    public function getClientId(): int { return $this->clientId; }
    public function getEtatDesLieuxId(): int { return $this->etatDesLieuxId; }
}
