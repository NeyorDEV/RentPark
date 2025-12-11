<?php

use modeleApi\Connection;

return function() {
    $dsn = 'mysql:host=127.0.0.1;port=3307;dbname=dbaltixier1;charset=utf8';
    $user = 'altixier1';
    $pass = 'achanger';

    return new Connection($dsn, $user, $pass);
};
