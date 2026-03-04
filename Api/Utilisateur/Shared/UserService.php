<?php
namespace App;
use App\Middleware\JsonResponse; 
use App\DTO\CreateUserDTO;
use App\DTO\UpdateUserDTO;
class UserService
{
    private UserRepository $repo;

    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAll(?string $nom)
    {
        return $this->repo->findAll($nom);
    }

    public function getOne(string $id)
    {
        $result = $this->repo->findById($id);

        if (empty($result)) {
            JsonResponse::error("Utilisateur non trouvé", 404);
        }

        return $result[0];
    }

    public function create(CreateUserDTO $dto)
    {
        $this->repo->create($dto);
    }

    public function delete(string $id)
    {
        $this->repo->delete($id);
    }

    public function findUserByUsername(string $username)
    {
        return $this->repo->findUserByUsername($username);
    }

    public function update(UpdateUserDTO $dto): bool {
        // Optionnel : vérifier si le user existe
        $existing = $this->repo->findById($dto->Id);
        if (!$existing) {
            throw new \Exception("Utilisateur introuvable");
        }
    
        return $this->repo->update($dto);
    }
}