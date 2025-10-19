<?php

//si controller pas objet
//  header('Location: controller/controller.php');

//si controller objet

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/vendor/autoload.php';


use controleur\FrontControleur;

$controller = new FrontControleur();
// instancie un front controller et le controller gère le reste 
// alto routeur 
?> 