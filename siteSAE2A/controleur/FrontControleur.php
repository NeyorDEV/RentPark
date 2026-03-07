<?php
namespace controleur;

use AltoRouter;
use controleur\AdminControleur;
use controleur\UserController;


class FrontControleur
{
    use RoleAwareTrait;

    public function __construct()
    {
        session_start();
        
    }

    public function run()
    {
        global $rep, $vues,$action; 
        $role = $this->getRole();
        $router = new AltoRouter();
        $router->setBasePath('/siteSAE2A'); 

        
        $router->map('GET|POST', '/index.php', 'homeCustomers');
        $router->map('GET|POST', '/voitures', 'listeVoitures');
        $router->map('GET|POST', '/home', 'homeCustomers');    

        $router->map('GET|POST', '/reservation', 'listeReservation');
        
       
        $router->map('GET|POST', '/utilisateurs', 'listeUtilisateurs');
        $router->map('GET|POST', '/clients', 'listeClients');



        $router->map('GET|POST', '/inscription', 'afficheInscription');
        $router->map('GET|POST', '/connection', 'afficheConnection');
        $router->map('GET|POST', '/deconnection', 'deconnecter');
        $router->map('GET|POST', '/dashboard', 'afficheDashboard');
        $router->map('GET|POST', '/cars', 'cars'); // suite au home, la recherche de vehicule pour réserver
        $router->map('GET|POST', '/reservationForm', 'reservationForm');
        $router->map('GET|POST', '/recapitulatif', 'afficheRecapitulatif');
        $router->map('GET|POST', '/parametres', 'afficheParametres');
        $router->map('GET|POST', '/finaliserReservation', 'finaliserReservation');
        $router->map('GET|POST', '/planning', 'affichePlanning');
        $router->map('GET|POST', '/homeCustomers', 'homeCustomers');// a voir
        
        $match = $router->match();
        if ($match) {
            $action = $match['target'];
        
            // ROUTES PUBLIQUES (User par défaut)
            $publicRoutes = [
                'afficheInscription',
                'afficheConnection',
                'deconnecter',
                'listeVoitures',
                'cars',
                'homeCustomers',
                'reservationForm',
                'afficheRecapitulatif',
                'finaliserReservation'
            ];
        
            // ROUTES USER CONNECTÉ
            $userRoutes = [
                'listeReservation',
                'afficheParametres'
            ];

            // ROUTES EMPLOYE
            $employeRoutes = [
                'affichePlanning',
                'listeClients',
                'listeUtilisateurs',
                'listeReservation',
                'listeVoitures'               
            ];

            // ROUTES ADMIN
            $adminRoutes=[
                'listeClients',
                'afficheDashboard',
                'listeUtilisateurs',

            ];         

            $find=false;
            /**
             * PUBLIC
             */
            if (in_array($action, $publicRoutes)) {
                $controleur = new UserController();
                $find=true;

            }
            if (in_array($action, $userRoutes)) {
                $this->checkUser();
                $controleur = new UserController();
                $find=true;
            }
            /**
             * EMPLOYÉ
             */
            if (in_array($action, $employeRoutes)){
                $this->checkEmploye();
                $controleur = new EmployeControleur();
                $find=true;
            }
            /**
             * ADMIN
             */
            if (in_array($action, $adminRoutes)) {
                $this->checkAdmin();
                $controleur = new AdminControleur();
                $find=true;
            }
            /**
            * LE RESTE → REFUS
             */
            if (!$find) {
                header('HTTP/1.1 404 NotFound');
                exit('Page non trouvée');
            }
            
            
    
            $controleur->$action();
        }

         
    }  
}  
?> 
    
