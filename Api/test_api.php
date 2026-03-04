<?php
require __DIR__ . '/vendor/autoload.php';

use App\DTO\CreateVehiculeDTO;

//  $_SERVER['USER'] = ['role' => 'user'];  pour tester les roles 

// 1. Simulation des données reçues
$data = [
    'NumSerie' => 'TEST' . time(),
    'Marque'   => 'Audi',
    'Nom'      => 'a4',
    'Prix'     => 150.00,
    'Energie'  => 'Diesel',
    'Boite'    => 'Automatique',
    'NbPlaces' => 5,
    'Categorie'=> 'Berline',
    'Transmission' => 'propulsion',
    'Etat'     => 'libre',
    'DateAchat' => '2024-03-04', // Format YYYY-MM-DD
    'DateExpirationControleTech' => '2026-03-04',
    'DateDernierControleTech' => '2024-01-01',
    'Puissance'=> '150',
    'Annee'    => 2022,
    'IdAssureur' => 1,
    'IdFournisseur' => 1
];

try {
    // \App\Security\RoleMiddleware::requireRole('admin');  pour tester le middleware de rôle 
    echo "--- Test de création du DTO ---\n";
    $dto = CreateVehiculeDTO::fromArray($data);
    echo "DTO créé avec succès !\n\n";

    echo "--- Test de Connexion BDD ---\n";
    $databaseFactory = require __DIR__ . '/config/database.php';
    $db = $databaseFactory();
    echo "Connexion BDD OK.\n\n";

    echo "--- Test d'insertion ---\n";
    $repo = new \App\VehiculeRepository($db);
    $service = new \App\VehiculeService($repo);
    
    $res = $service->create($dto);
    echo $res ? "Insertion RÉUSSIE dans la BDD !\n" : "L'insertion a échoué.\n";

} catch (\Throwable $e) {
    echo "ERREUR DÉTECTÉE :\n";
    echo "Message : " . $e->getMessage() . "\n";
    echo "Fichier : " . $e->getFile() . " (Ligne " . $e->getLine() . ")\n";
}