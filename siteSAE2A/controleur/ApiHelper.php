<?php
namespace controleur;
use GuzzleHttp\Client;

function getApiClient(): Client
{
    return new Client([
        'base_uri' => 'https://codefirst.iut.uca.fr/kubernetes/iut-inf63-projets-etudiants-rentpark/rentpark-api-pod/',
        'timeout' => 30.0,
    ]);
}

function getClientApiClient(): Client
{
    return new Client([
        'base_uri' => 'https://codefirst.iut.uca.fr/kubernetes/iut-inf63-projets-etudiants-rentpark/rentpark-clients-pod/',
        'timeout'  => 30.0,
    ]);
}