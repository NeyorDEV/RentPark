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
function getAuthApiClient(): Client
{
    return new Client([
        'base_uri' => 'https://codefirst.iut.uca.fr/kubernetes/iut-inf63-projets-etudiants-rentpark/rentpark-auth-pod/',
        'timeout'  => 30.0,
    ]);
}
function getContratApiClient(): Client
{
    return new Client([
        'base_uri' => 'https://codefirst.iut.uca.fr/kubernetes/iut-inf63-projets-etudiants-rentpark/rentpark-contrat-pod/',
        'timeout'  => 30.0,
    ]);
}
function getPlanningApiClient(): Client
{
    return new Client([
        'base_uri' => 'https://codefirst.iut.uca.fr/kubernetes/iut-inf63-projets-etudiants-rentpark/rentpark-planning-pod/',
        'timeout'  => 30.0,
    ]);
}
