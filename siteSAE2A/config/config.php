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

$vues['flotte']  = 'flotte.twig';
$vues['user']  = 'view/viewUtilisateur.php';
$vues['erreur'] = 'view/viewErreur.php';
$vues['reservation'] = 'view/viewReservation.php';
$vues['inscription'] = 'view/viewInscription.php';
$vues['connection'] = 'view/viewConnection.php';
$vues['dashboard'] = 'view/viewDashboard.php';
$vues['homeCustomers'] = 'view/viewHomeCustomers.php';
$vues['cars'] = 'view/viewCars.php';
$vues['recapitulatif'] = 'view/viewRecapitulatif.php';
$vues['parametres'] = 'view/viewParametres.php';


$action = "RAS";

$role = '';