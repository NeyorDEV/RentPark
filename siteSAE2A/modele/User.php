<?php
// classes/User.php

namespace modele;

class User
{
    private ?int $id;
    private string $username;
    private string $password;
    private string $role;

    public function __construct(?int $id, string $username, string $password,string $role)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
    }

    public function getId(): ?int { return $this->id; }
    public function getUsername(): string { return $this->username; }
    public function getPassword(): string { return $this->password; }

    public function getRole(): string { return $this->role; }

    
}
