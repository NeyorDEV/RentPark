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
        global $rep, $vues,$action; // tes globals

        $role = $_SESSION['role'] ?? 'admin';

       

        $router = new AltoRouter();
        $router->setBasePath('/siteSAE2A'); // ton dossier projet

        // Route voitures (liste)
        $router->map('GET|POST', '/voitures', 'listeVoitures');

        // Route utilisateurs (liste) - admin uniquement
        $router->map('GET|POST', '/utilisateurs', 'listeUtilisateur');

        // Match
        $match = $router->match();
        if (!$match) { echo "404"; die; }
        if ($match) {
            $action=$match['target'];
            
            $controleur = new AdminControleur();
        }  

         
    }  
}  
 
?>    
