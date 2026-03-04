<?php

namespace App;
use App\DTO\CreateUserDTO;
use App\DTO\UpdateUserDTO;

class UserRepository
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function findAll(?string $nom = null)
    {
        $sql = "SELECT * FROM users";
        $params = [];

        if ($nom) {
            $sql .= " WHERE Nom LIKE :nom";
            $params[':nom'] = ['%' . $nom . '%', \PDO::PARAM_STR];
        }

        $this->conn->executeQuery($sql, $params);
        return $this->conn->getResults();
    }

    public function findById(string $id)
    {
        $this->conn->executeQuery(
            "SELECT * FROM users WHERE Id = :id",
            [':id' => [$id, \PDO::PARAM_STR]]
        );

        return $this->conn->getResults();
    }

    public function create(CreateUserDTO $dto)
    {
        $sql = "INSERT INTO users (
            Id, Username, Password, Role
        ) VALUES (
            :id, :username, :password, :role
        )";

        $params = [
            ':id' => [$dto->Id, \PDO::PARAM_STR],
            ':username' => [$dto->Username, \PDO::PARAM_STR],
            ':password' => [$dto->Password, \PDO::PARAM_STR],
            ':role' => [$dto->Role, \PDO::PARAM_STR]
        ];

        $this->conn->executeQuery($sql, $params);
    }

    public function update(UpdateUserDTO $dto): bool 
    {
        $sql = "UPDATE users SET 
                    Username = :username, 
                    Password = :password, 
                    Role = :role
                WHERE Id = :id";

        $params = [
            ':id'       => [$dto->Id, \PDO::PARAM_STR],
            ':username' => [$dto->Username, \PDO::PARAM_STR],
            ':password' => [$dto->Password, \PDO::PARAM_STR],
            ':role'     => [$dto->Role, \PDO::PARAM_STR]
        ];

        $this->conn->executeQuery($sql, $params);
        return true;
    }

    public function delete(string $id)
    {
        $this->conn->executeQuery(
            "DELETE FROM users WHERE Id = :id",
            [':id' => [$id, \PDO::PARAM_STR]]
        );
    }

    public function findUserByUsername(string $username) 
    {
        $this->conn->executeQuery(
            "SELECT id, username, password, role FROM users WHERE username = :username",
            [':username' => [$username, \PDO::PARAM_STR]]
        );
        
        $results = $this->conn->getResults();
        
        return !empty($results) ? $results[0] : null;
    }
}
