<?php
namespace App\DTO;
use App\Middleware\JsonResponse; 

class CreateUserDTO
{
    public int $Id;
    public string $Username;
    public string $Password;
    public string $Role;

    public static function fromArray(array $data): self
    {
        if (empty($data['Username'])) {
            JsonResponse::error("Username obligatoire", 422);
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
