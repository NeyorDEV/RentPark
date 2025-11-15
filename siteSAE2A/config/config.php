<?php

//gen
$rep = __DIR__ . '/../';

// liste des modules à inclure

//$dConfig['includes']= array('controleur/Validation.php');

//BD

$user = 'altixier1';
$pass = 'achanger';
$dsn = 'mysql:host=127.0.0.1;port=3307;dbname=dbaltixier1;charset=utf8';

//Vues

$vues['flotte']  = 'viewFlotte.twig';
$vues['user']  = 'viewUtilisateur.twig';
$vues['erreur'] = 'view/viewErreur.php';
$vues['reservation'] = 'viewReservation.twig';
$vues['inscription'] = 'view/viewInscription.php';
$vues['connection'] = 'view/viewConnection.php';

$action = "RAS";

$role = '';