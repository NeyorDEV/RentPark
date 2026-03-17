<?php
namespace controleur;
use GuzzleHttp\Client;

function getApiClient(): Client
{
    return new Client([
        'base_uri' => 'https://codefirst.iut.uca.fr/kubernetes/iut-inf63-projets-etudiants-rentpark/rentpark-api-pod/',
        'timeout' => 30.0,
        'headers' => [
            'Authorization' => 'Bearer ' . ($_SESSION['api_token'] ?? ''),
            'Content-Type' => 'application/json'
        ]
    ]);
}