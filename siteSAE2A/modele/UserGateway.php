<?php
namespace modele;
class UserGateway {
    private Connection $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public function addUser(User $user): void
    {
        $query = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
        $params = [
            ':username' => [$user->getUsername(), \PDO::PARAM_STR],
            ':password' => [$user->getPassword(), \PDO::PARAM_STR], // déjà hashé
            ':role'     => [$user->getRole(), \PDO::PARAM_STR],
        ];

        $this->connection->executeQuery($query, $params);
    }
    
}
?>

