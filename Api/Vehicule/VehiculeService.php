<?php
namespace App;
use App\Middleware\JsonResponse; 
use App\DTO\CreateVehiculeDTO;
use App\DTO\UpdateVehiculeDTO;
class VehiculeService
{
    private VehiculeRepository $repo;

    public function __construct(VehiculeRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAll(?string $nom)
    {
        return $this->repo->findAll($nom);
    }

    public function getOne(string $numSerie)
    {
        $result = $this->repo->findByNumSerie($numSerie);

        if (empty($result)) {
            JsonResponse::error("Véhicule non trouvé", 404);
        }

        return $result[0];
    }

    public function create(CreateVehiculeDTO $dto)
    {
        $this->repo->create($dto);
    }

    public function delete(string $numSerie)
    {
        $this->repo->delete($numSerie);
    }

    public function findUserByUsername(string $username)
    {
        return $this->repo->findUserByUsername($username);
    }

    public function update(UpdateVehiculeDTO $dto): bool {
        // Optionnel : vérifier si le véhicule existe
        $existing = $this->repo->findByNumSerie($dto->NumSerie);
        if (!$existing) {
            throw new \Exception("Véhicule introuvable");
        }
    
        return $this->repo->update($dto);
    }
}