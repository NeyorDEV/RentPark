<?php

//si controller pas objet
//  header('Location: controller/controller.php');

//si controller objet

//chargement config
require_once(__DIR__ . '/config/config.php');

//chargement autoloader pour autochargement des classes
require_once(__DIR__ . '/config/Autoload.php');
Autoload::charger();


$action = $_POST['action'] ?? $_GET['action'] ?? null;

if ($action === 'inscription') {
    $controller = new Controleur();
    $controller->inscription($dVueErreur); // ici tu traites le POST
    
    exit;
}

$role = $_SESSION['role'] ?? 'admin'; // 'admin', 'user', 'guest'

// Selon le rôle, on instancie le bon contrôleur
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