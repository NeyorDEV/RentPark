<?php
namespace modele;
class UserGateway {
    private Connection $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public function login(User $user) :void
    {
        $query = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
        $params = [
            ':username' => [$user->getUsername(), \PDO::PARAM_STR],
            ':password' => [$user->getPassword(), \PDO::PARAM_STR], // déjà hashé
            ':role'     => [$user->getRole(), \PDO::PARAM_STR],
        ];
        $this->connection->executeQuery($query, $params);
    }


    public function addUser(string $user, $pass, $role): void
    {
        $query = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
        $params = [
            ':username' => [$user, \PDO::PARAM_STR],
            ':password' => [$pass, \PDO::PARAM_STR],
            ':role' => [$role, \PDO::PARAM_STR],
        ];

        $this->connection->executeQuery($query, $params);
    }

    public function deleteUser(int $id): void {
        $query = "DELETE FROM users WHERE id = :id";
        $params = [
            ':id' => [$id, \PDO::PARAM_INT]
        ];
        $this->connection->executeQuery($query, $params);
    }

    public function getAllUser(): array {
        $this->connection->executeQuery("SELECT * FROM users");
        return $this->connection->getResults();
    }

    public function rechercherUtilisateur(string $motCle): array {
        $query = "SELECT * FROM users WHERE username LIKE :q";
        $params = [
            ':q' => ["%$motCle%", \PDO::PARAM_STR]
        ];

        $this->connection->executeQuery($query, $params);


        return $this->connection->getResults();
    }
}
?>

