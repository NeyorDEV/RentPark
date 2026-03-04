<?php
// Api/routes.php

use Slim\App;
use App\VehiculeController; 
use App\UserController;
use App\Security\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    // 1. Route publique pour le login
    $app->post('/login', [VehiculeController::class, 'login']);
    $app->get('/getAll', [UserController::class, 'getAll']);

    // 2. Groupe de routes protégées
    $app->group('/api', function (RouteCollectorProxy $group) {
        
        // --- Collection de véhicules ---
        $group->get('/vehicules', [VehiculeController::class, 'list']);
        $group->post('/vehicules', [VehiculeController::class, 'create']); // Création (POST)

        // --- Ressource unique (un véhicule) ---
        // On utilise l'ID (ou numSerie) pour toutes les actions sur UN véhicule
        $group->group('/vehicules/{id}', function (RouteCollectorProxy $vehicle) {
            $vehicle->get('', [VehiculeController::class, 'getOne']);    // Voir (GET)
            $vehicle->put('', [VehiculeController::class, 'update']);   // Modifier (PUT)
            $vehicle->delete('', [VehiculeController::class, 'delete']); // Supprimer (DELETE)
        });

        // --- Collection d'utilisateurs ---
        $group->get('/utilisateurs', [UserController::class, 'list']);
        $group->post('/utilisateurs', [UserController::class, 'create']);
        $group->get('/utilisateurs/{id}', [UserController::class, 'getOne']);
        $group->put('/utilisateurs/{id}', [UserController::class, 'update']);
        $group->delete('/utilisateurs/{id}', [UserController::class, 'delete']);  


    })->add(new AuthMiddleware()); 


};