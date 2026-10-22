<?php

namespace SiteSAE2A\Config;

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
$vues['client']  = 'view/viewUtilisateur.php';
$vues['reservation'] = 'view/viewReservation.php';
$vues['inscription'] = 'view/viewInscription.php';
$vues['connection'] = 'view/viewConnection.php';
$vues['dashboard'] = 'view/viewDashboard.php';
$vues['homeCustomers'] = 'view/viewHomeCustomers.php';
$vues['cars'] = 'view/viewCars.php';
$vues['recapitulatif'] = 'view/viewRecap.php';
$vues['parametres'] = 'view/viewParametres.php';
$vues['reservationForm'] = 'view/viewReservationForm.php';
$vues['confirmationSucces'] = 'view/confirmationSucces.php';
$vues['planning'] = 'view/viewPlanning.php';
$vues['erreur'] = 'view/erreur.php';



$action = "RAS";

$role="unknow";

// Mot de passe d'application SMTP : ne jamais l'écrire dans le dépôt.
// Il est lu depuis la variable d'environnement SMTP_PASS, ou depuis
// config/config.local.php (non versionné, voir config.local.example.php).
$smtp_pass = getenv('SMTP_PASS') ?: '';
if (is_file(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}
