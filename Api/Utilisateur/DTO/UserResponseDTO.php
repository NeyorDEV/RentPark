<?php

namespace App\DTO;
class UserResponseDTO
{
    public int $id;
    public string $username;
    public string $role;
    public array $_links;

    public function __construct($user, array $links)
    {
        $this->id = $user['id'];
        $this->username = $user['username'];
        $this->role = $user['role'];
        $this->_links = $links;
    }
}