<?php
namespace controleur;

use AltoRouter;

class FrontControleur
{
    public function __construct()
    {
        session_start();
    }

    public function run()
    {
        global $rep, $vues,$action; 

        $role = $_SESSION['role'] ?? 'admin';

       
        $router = new AltoRouter();
        $router->setBasePath('/siteSAE2A'); 

        
        $router->map('GET|POST', '/voitures', 'listeVoitures');
        $router->map('GET|POST', '/home', 'homeCustomers');    

        $router->map('GET|POST', '/reservation', 'listeReservation');
        
       
        $router->map('GET|POST', '/utilisateurs', 'listeUtilisateur');

        $router->map('GET|POST', '/inscription', 'afficheInscription');
        $router->map('GET|POST', '/connection', 'afficheConnection');
        $router->map('GET|POST', '/deconnection', 'deconnecter');
        $router->map('GET|POST', '/dashboard', 'afficheDashboard');
        $router->map('GET|POST', '/homeCustomers', 'homeCustomers');// a voir
        $router->map('GET|POST', '/cars', 'cars'); // suite au home, la recherche de vehicule pour réserver
        $router->map('GET|POST', '/recapitulatif', 'afficheRecapitulatif');
        $router->map('GET|POST', '/parametres', 'afficheParametres');
        $router->map('GET|POST', '/planning', 'affichePlanning');
        
        $match = $router->match();
        if (!$match) { echo "404"; die; }
        if ($match) {
            $action=$match['target'];
            
            $controleur = new AdminControleur();
        }  

         
    }  
}  
 
?>    
