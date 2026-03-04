<?php
namespace App\DTO;
use App\Middleware\JsonResponse; 

class CreateVehiculeDTO
{
    public string $NumSerie;
    public string $Energie;
    public int $NbPlaces;
    public string $Categorie;
    public string $Transmission;
    public string $Boite;
    public string $Etat;
    public string $Puissance;
    public ?string $DateAchat;
    public ?string $DateExpirationControleTech;
    public ?string $DateDernierControleTech;
    public string $Marque;
    public string $Nom;
    public int $Annee;
    public int $IdAssureur;
    public int $IdFournisseur;
    public ?string $ImagePath;
    public ?string $Couleur;
    public ?float $Prix;

    public static function fromArray(array $data): self
    {
        if (empty($data['NumSerie'])) {
            JsonResponse::error("NumSerie obligatoire", 422);
        }

        $dto = new self();
        foreach ($data as $key => $value) {
            if (property_exists($dto, $key)) {
                $dto->$key = $value;
            }
        }

        return $dto;
    }
}