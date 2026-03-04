<?php
namespace App\DTO;

class UpdateUserDTO {
    public function __construct(
        public string $Id, // Identifiant (non modifiable dans le SET)*
        public ?string $Username = null,
        public ?string $Password = null,
        public ?string $Role = null
    ) {}

    public static function fromArray(string $id, array $data): self {
        return new self(
            $id,
            $data['Username'] ?? null,
            $data['Password'] ?? null,
            $data['Role'] ?? null
        );
    }
}