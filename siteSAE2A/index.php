<?php

//si controller pas objet
//  header('Location: controller/controller.php');

//si controller objet

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use controleur\Controleur; 
use controleur\UserController;


// Récup action + rôle
$action = $_POST['action'] ?? $_GET['action'] ?? null;
$role   = $_SESSION['role'] ?? 'admin'; // 'admin', 'user', 'guest'


// --- ROUTES SPÉCIFIQUES AVANT DISPATCH PAR RÔLE ---
if ($action === 'inscription') {
    $controller = new Controleur();
    $controller->inscription($dVueErreur);
    
    exit;
}
if ($action === 'ajouterReservation') {
    require_once __DIR__ . '/controleur/Controleur.php';
    new Controleur(); 
    exit;                 
}
// ACCÈS à la liste des réservations
if ($action === 'rechercherReservation') {
    $_GET['action'] = $_REQUEST['action'] = 'rechercherReservation';
    require_once __DIR__ . '/controleur/Controleur.php';
    new Controleur();
    exit;
}


// --- DISPATCH PAR RÔLE PAR DÉFAUT ---
switch ($role) {
    case 'admin':
        require_once(__DIR__ . '/controleur/Controleur.php');
        $controller = new Controleur();
        break;

    case 'user':
        require_once(__DIR__ . '/controleur/UserController.php');
        $controller = new UserController();
        break;

    default: // guest ou non connecté
        require_once(__DIR__ . '/controleur/UserController.php');
        $controller = new UserController();
        break;
}

// instancie un front controller et le controller gère le reste 
// alto routeur 
?> 