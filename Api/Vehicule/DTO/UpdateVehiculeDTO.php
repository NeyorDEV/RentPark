<?php
namespace App\DTO;

class UpdateVehiculeDTO {
    public function __construct(
        public string $NumSerie, // Identifiant (non modifiable dans le SET)
        public ?float $Prix = null,
        public ?string $Energie = null,
        public ?string $Boite = null,
        public ?int $NbPlaces = null,
        public ?string $Categorie = null,
        public ?string $Transmission = null,
        public ?string $Etat = null,
        public ?string $Puissance = null,
        public ?string $Couleur = null,
        public ?string $ImagePath = null
    ) {}

    public static function fromArray(string $numSerie, array $data): self {
        return new self(
            $numSerie,
            $data['Prix'] ?? null,
            $data['Energie'] ?? null,
            $data['Boite'] ?? null,
            $data['NbPlaces'] ?? null,
            $data['Categorie'] ?? null,
            $data['Transmission'] ?? null,
            $data['Etat'] ?? null,
            $data['Puissance'] ?? null,
            $data['Couleur'] ?? null,
            $data['ImagePath'] ?? null
        );
    }
}