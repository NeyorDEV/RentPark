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

            $roleControleur = [
                'admin'   => fn() => new AdminControleur(),
                'employe' => fn() => new EmployeControleur(),
                'user'    => fn() => new UserController(),
                'public'  => fn() => new UserController(),
            ];

            // Hiérarchie des rôles (du plus haut au plus bas)
            $hierarchie = ['admin', 'employe', 'user', 'public'];

            // Toutes les routes par rôle minimum requis
            $routesParRole = [
                'public'  => $publicRoutes,
                'user'    => $userRoutes,
                'employe' => $employeRoutes,
                'admin'   => $adminRoutes,
            ];

            $find = false;

            foreach ($hierarchie as $roleReqis) {
                if (!in_array($action, $routesParRole[$roleReqis])) {
                    continue; // Cette route n'appartient pas à ce niveau
                }

                // Vérifier que l'utilisateur a au moins ce niveau
                if ($this->hasRole($role, $roleReqis, $hierarchie)) {
                    $this->checkRole($roleReqis);
                    // 👇 Utiliser le contrôleur DU RÔLE DE LA ROUTE, pas celui de l'utilisateur
                    $controleur = $roleControleur[$roleReqis]();
                    $find = true;
                    break;
                }
            }
            $controleur->$action();
        }

        
         
    }  
    private function hasRole(string $roleUser, string $roleRequis, array $hierarchie): bool {
        // Un rôle plus à gauche dans la hiérarchie a plus de droits
        return array_search($roleUser, $hierarchie) <= array_search($roleRequis, $hierarchie);
    }

    private function checkRole(string $role): void {
        match($role) {
        'admin'   => $this->checkAdmin(),
        'employe' => $this->checkEmploye(),
        'user'    => $this->checkUser(),
        default   => null,
        };
    }
}
?> 
    
