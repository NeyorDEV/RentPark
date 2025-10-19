<?php

//si controller pas objet
//  header('Location: controller/controller.php');

//si controller objet

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/vendor/autoload.php';


use controleur\FrontControleur;


$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig   = new \Twig\Environment($loader, [
    'cache' => false,
    'auto_reload' => true,
]);

$controller = new FrontControleur();
$controller->run();

// alto routeur 
?> 