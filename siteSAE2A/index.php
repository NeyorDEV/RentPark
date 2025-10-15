<?php

//si controller pas objet
//  header('Location: controller/controller.php');

//si controller objet

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use controleur\Controleur; 
use controleur\UserController;


$action = $_POST['action'] ?? $_GET['action'] ?? null;

if ($action === 'inscription') {
    $controller = new Controleur();
    $controller->inscription($dVueErreur); // ici tu traites le POST à améliorer après pour savoir ou le mettre ect 
    
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