<?php
namespace controleur;

use AltoRouter;
use controleur\AdminControleur;
use controleur\UserController;


class FrontControleur
{
    public function __construct()
    {
        session_start();
    }

    public function run()
    {
        global $rep, $vues,$action; 

        $role = $_SESSION['role'] ?? 'user';

       
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
        $router->map('GET|POST', '/homeCustomers', 'homeCustomers');// a voir
        $router->map('GET|POST', '/cars', 'cars'); // suite au home, la recherche de vehicule pour réserver
        $router->map('GET|POST', '/reservationForm', 'reservationForm');
        $router->map('GET|POST', '/recapitulatif', 'afficheRecapitulatif');
        $router->map('GET|POST', '/parametres', 'afficheParametres');
        $router->map('GET|POST', '/finaliserReservation', 'finaliserReservation');
        $router->map('GET|POST', '/planning', 'affichePlanning');
        
        $match = $router->match();
        if ($match) {
            $action = $match['target'];
        
            // ROUTES PUBLIQUES (User par défaut)
            $publicRoutes = [
                'homeCustomers',
                'cars',
                'afficheConnection',
                'afficheInscription',
                'deconnecter',
                'reservationForm',
                'afficheRecapitulatif',
                'finaliserReservation'
            ];
        
            // ROUTES USER CONNECTÉ
            $userRoutes = [
                'connection',
                'afficheParametres',
                'listeReservation'
            ];

            $employeRoutes = [
                'listeVoitures',
                
                            'listeUtilisateurs',
                'listeClients',
                'affichePlanning',
                'afficheParametres'
                
            ];
        
            if ($role === 'admin') {
                $controleur = new AdminControleur();
            }
            /**
             * EMPLOYÉ
             */
            elseif (in_array($action, $employeRoutes)) {
    
                if ($role !== 'employe') {
                    header('HTTP/1.1 403 Forbidden');
                    exit('Accès refusé');
                }
    
                $controleur = new EmployeControleur();
            }
            /**
             * USER CONNECTÉ
             */
            elseif (in_array($action, $userRoutes)) {
    
                if (!isset($_SESSION['username'])) {
                    header('Location: /siteSAE2A/home');
                    exit;
                }
    
                $controleur = new UserController();
            }
            /**
             * PUBLIC
             */
            elseif (in_array($action, $publicRoutes)) {
                $controleur = new UserController();
            }
            /**
             * LE RESTE → REFUS
             */
            else {
                header('HTTP/1.1 403 Forbidden');
                exit('Accès refusé');
            }
    
            $controleur->$action();
        }

         
    }  
}  
 
?>    
