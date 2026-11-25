<?php

use modeleApi\Connection;

// Config via variables d'environnement (valeurs par défaut = base locale Docker, voir docker-compose.yml)
return function () {
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3307';
    $name = getenv('DB_NAME') ?: 'dbaltixier1';
    $user = getenv('DB_USER') ?: 'altixier1';
    $pass = getenv('DB_PASS');
    if ($pass === false) {
        $pass = 'achanger';
    }

    return new Connection("mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4", $user, $pass);
};
