<?php
namespace modeleApi\Gateway;

class UserGateway {

    private \PDO $conn;
    private string $table = 'users';

    public function __construct(\PDO $conn) {
        $this->conn = $conn;
    }

    public function insert(array $data): int
    {
        
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("
            INSERT INTO users (username, password, role)
            VALUES (:username, :password, :role)
        ");
        $stmt->execute([
            ':username' => $data['username'],
            ':password' => $hashedPassword,
            ':role' => $data['role']
        ]);

        return (int)$this->conn->lastInsertId();
    }

    public function findAll() {
        $stmt = $this->conn->query("SELECT id, username, role FROM {$this->table}");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT id, nom, FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function update($id, array $data) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET nom = :nom WHERE id = :id");
        $stmt->execute([
            'nom' => $data['nom'],
            'email' => $data['email'],
            'id' => $id
        ]);
        return $this->findById($id);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return true;
    }
}