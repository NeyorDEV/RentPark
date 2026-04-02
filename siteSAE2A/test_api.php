<?php
require 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
try {
    $client = new Client([
        'base_uri' => 'https://codefirst.iut.uca.fr/kubernetes/iut-inf63-projets-etudiants-rentpark/rentpark-auth-pod/',
        'timeout'  => 60.0,
        'verify'   => false,
        'http_errors' => true,
    ]);
    $response = $client->post('login', [
        'json' => ['username' => 'je', 'password' => 'test']
    ]);
    $data = json_decode($response->getBody()->getContents(), true);
    echo 'Success: ' . json_encode($data) . PHP_EOL;
} catch (RequestException $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>