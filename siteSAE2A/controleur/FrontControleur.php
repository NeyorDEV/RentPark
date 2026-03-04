<?php
namespace controleur;

use AltoRouter;
// Pas besoin de "use" si les classes sont dans le même namespace "controleur"
// mais on les laisse par sécurité si ton autoloader est strict.
use controleur\AdminControleur;
use controleur\UserController;



class FrontControleur
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function run()
    {
  
     
        // On récupère le rôle. 'visitor' est plus propre que 'user' par défaut.
        $role = $_SESSION['role'] ?? 'user';

        $router = new AltoRouter();
        $router->setBasePath('/siteSAE2A'); 

        // --- MAPPING DES ROUTES ---
        // Format : Method, Route, Target (Action)
        
        // Routes Authentification
        $router->map('GET|POST', '/connection', 'afficheConnection');
        $router->map('GET|POST', '/inscription', 'afficheInscription');
        $router->map('GET',      '/deconnection', 'deconnecter');

        // Routes Admin (Gestion)
        $router->map('GET|POST', '/dashboard',    'afficheDashboard');
        $router->map('GET|POST', '/voitures',     'listeVoitures');
        $router->map('GET|POST', '/reservation',  'listeReservation');
        $router->map('GET|POST', '/utilisateurs', 'listeUtilisateur');
        $router->map('GET|POST', '/planning',     'affichePlanning');
        $router->map('GET|POST', '/parametres',   'afficheParametres');

        // Routes Client / Public
        $router->map('GET',      '/',                    'homeCustomers');
        $router->map('GET',      '/home',                'homeCustomers');
        $router->map('GET',      '/cars',                'cars');
        $router->map('GET|POST', '/reservationForm',     'reservationForm');
        $router->map('GET|POST', '/recapitulatif',       'afficheRecapitulatif');
        $router->map('GET|POST', '/finaliserReservation','finaliserReservation');
        
        // ... mapping ...

$match = $router->match();
if ($match) {
    $action = $match['target'];
    $dVueErreur = [];

    // Liste des routes réservées UNIQUEMENT à l'admin
    $adminRoutes = [
        'listeVoitures', 'listeReservation', 'listeUtilisateur', 
        'affichePlanning', 'afficheDashboard', 'afficheParametres'
    ];

    // --- LOGIQUE DE SÉCURITÉ ---

    if (in_array($action, $adminRoutes)) {
        // Cas 1 : Route Admin -> Vérification stricte
        if ($role !== 'admin') {
            header('Location: /siteSAE2A/connection');
            exit;
        }
        $controleur = new AdminControleur();
    } 
    else {
        // Cas 2 : Route Public -> Tout le monde a le droit !
        // L'admin peut voir les voitures, le home, etc.
        $controleur = new UserController();
    }

    // --- EXÉCUTION ---
    if (method_exists($controleur, $action)) {
        $controleur->$action($dVueErreur); 
    } else {
        exit("Erreur : La méthode $action n'existe pas.");
    }

} else {
    // Route non trouvée -> Accueil
    $controleur = new UserController();
    $controleur->homeCustomers([]);
}
    }  
}