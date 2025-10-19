<?php
namespace controleur;

//si controller pas objet
//  header('Location: controller/controller.php');

//si controller objet


use controleur\AdminControleur; 
use controleur\UserController;

class FrontControleur
{
    public function __construct()
    {
        global $rep, $vues, $user, $pass, $dsn;
        session_start();

        $dVueErreur = [];

        $role = $_SESSION['role'] ?? 'admin'; // 'admin', 'user', 'guest'

        // Selon le rôle, on instancie le bon contrôleur
        switch ($role) {
            case 'admin':
                require_once(__DIR__ . '/AdminControleur.php');
                $controller = new AdminControleur();
                break;
        
            case 'user':
                require_once(__DIR__ . '/UserController.php');
                $controller = new UserController();
                break;
        
            default: // guest ou non connecté
                require_once(__DIR__ . '/UserController.php');
                $controller = new UserController();
                break;
        }

        $action = $_POST['action'] ?? $_GET['action'] ?? null;

        if ($action === 'inscription') {
             $controller = new AdminControleur();
             $controller->inscription($dVueErreur); // ici traimenet  du  POST à améliorer après pour savoir ou le mettre ect 
    
             exit;
            }

        exit(0);
    }
}
?>






