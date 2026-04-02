<?php
namespace controleur;


use AltoRouter;
use controleur\AdminControleur;
use controleur\UserController;
// N'oublie pas d'importer EmployeControleur si tu l'utilises
// use controleur\EmployeControleur; 


class FrontControleur
{
    use RoleAwareTrait;

    public function __construct()
    {
        session_start();
    }

    public function run()
    {
        global $rep, $vues, $action; 
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
        $router->map('GET|POST', '/cars', 'cars'); 
        $router->map('GET|POST', '/reservationForm', 'reservationForm');
        $router->map('GET|POST', '/recapitulatif', 'afficheRecapitulatif');
        $router->map('GET|POST', '/parametres', 'afficheParametres');
        $router->map('GET|POST', '/finaliserReservation', 'finaliserReservation');
        $router->map('GET|POST', '/planning', 'affichePlanning');
        $router->map('GET|POST', '/homeCustomers', 'homeCustomers');
        
        $match = $router->match();
        
        if ($match) {
            $action = $match['target'];
        
            $publicRoutes = [
                'afficheInscription', 'afficheConnection', 'deconnecter',
                'listeVoitures', 'cars', 'homeCustomers', 'reservationForm',
                'afficheRecapitulatif', 'finaliserReservation'
            ];
        
            $userRoutes = [
                'listeReservation', 'afficheParametres'
            ];

            $employeRoutes = [
                'affichePlanning', 'listeClients', 'listeUtilisateurs',
                'listeReservation', 'listeVoitures'               
            ];

            $adminRoutes=[
                'listeClients', 'afficheDashboard', 'listeUtilisateurs',
            ];         

            $roleControleur = [
                'admin'   => fn() => new AdminControleur(),
                'employe' => fn() => new EmployeControleur(), // Assure-toi que la classe existe
                'user'    => fn() => new UserController(),
                'public'  => fn() => new UserController(),
            ];

            $hierarchie = ['admin', 'employe', 'user', 'public'];

            $routesParRole = [
                'public'  => $publicRoutes,
                'user'    => $userRoutes,
                'employe' => $employeRoutes,
                'admin'   => $adminRoutes,
            ];

            $find = false;

            foreach ($hierarchie as $roleReqis) {
                if (!in_array($action, $routesParRole[$roleReqis])) {
                    continue; 
                }

                if ($this->hasRole($role, $roleReqis, $hierarchie)) {
                    $this->checkRole($roleReqis);
                    $controleur = $roleControleur[$roleReqis]();
                    $find = true;
                    break;
                }
            }

            if ($find && method_exists($controleur, $action)) {
                $controleur->$action();
            } else {
                echo "Erreur : Action non autorisée ou méthode introuvable.";
            }

        } else {
            // GESTION ERREUR 404 : Route non trouvée (Important !)
            echo "<h1>Erreur 404</h1>";
            echo "<p>La page demandée est introuvable. URL : " . htmlspecialchars($_SERVER['REQUEST_URI']) . "</p>";
        }
    }  

    private function hasRole(string $roleUser, string $roleRequis, array $hierarchie): bool {
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