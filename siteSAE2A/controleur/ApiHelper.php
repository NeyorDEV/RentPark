<?php
namespace controleur;
use GuzzleHttp\Client;

/**
 * Tous les "pods" pointent vers la même API.
 * Local : http://localhost:8880/ (défaut). Déployé : définir API_BASE_URL.
 */
function makeApiClient(): Client
{
    $base = getenv('API_BASE_URL') ?: 'http://localhost:8880/';
    return new Client([
        'base_uri' => rtrim($base, '/') . '/',
        'timeout'  => 30.0,
    ]);
}

function getApiClient(): Client { return makeApiClient(); }
function getClientApiClient(): Client { return makeApiClient(); }
function getAuthApiClient(): Client { return makeApiClient(); }
function getContratApiClient(): Client { return makeApiClient(); }
function getPlanningApiClient(): Client { return makeApiClient(); }
function getRappelApiClient(): Client { return makeApiClient(); }
function getUserApiClient(): Client { return makeApiClient(); }
