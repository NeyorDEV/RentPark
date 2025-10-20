<?php
namespace controleur;
use modele\Connection;
use modele\UserGateway;
use modele\VehicleGateway;
use modele\User;
use config\Validation;
class AdminControleur
{
    private Connection $connection;
    private VehicleGateway $gateway;

    private UserGateway $userGateway;

    public function __construct()
    {
        global $rep, $vues, $user, $pass, $dsn,$action;

        $dVueEreur = [];

        try {
            
            $this->connection = new Connection($dsn, $user, $pass);

            
            $this->gateway = new VehicleGateway($this->connection);
            $this->userGateway = new UserGateway($this->connection);

            

            switch ($action) {
                case "listeVoitures":
                    $this->listeVoitures($dVueEreur);
                    break;
                case "ajouterVoiture":
                    $this->ajouterVoiture($dVueEreur);
                    break;

                case "supprimerVoiture":
                    $this->supprimerVoiture($dVueEreur);
                    break;
                
                case "modifierVoiture":
                    $this->modifierVoiture($dVueEreur);
                    break;

                case "inscription":
                    $this->inscription( $dVueEreur);
                    break;
                case "connection":
                    $this->connection( $dVueEreur);
                    break;
                case "rechercherVoitures":
                    $this->rechercherVoitures($dVueEreur);
                    break;

                case "ajouterUtilisateur":
                    $this->ajouterUtilisateur( $dVueEreur);
                    break;

                case "supprimerUtilisateur":
                    $this->supprimerUtilisateur( $dVueEreur);
                    break;

                case "rechercherUtilisateur":
                    $this->rechercherUtilisateur($dVueEreur);
                    break;

                case "listeUtilisateur":
                    $this->listeUtilisateur($dVueEreur);
                    break;
                    
            }

        } catch (\PDOException $e) {
            $dVueEreur[] = "Erreur BDD : " . $e->getMessage();
            $this->afficherVue('erreur', $dVueEreur, $results = null,'admin');
        }

        exit(0);
    }

    public function listeVoitures(array $dVueEreur)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sousAction = $_POST['action'] ?? '';

            switch ($sousAction) {
                case 'ajouterVoiture':
                    $this->ajouterVoiture($dVueEreur);
                    break;

                case 'supprimerVoiture':
                    $this->supprimerVoiture($dVueEreur);
                    break;

                case 'modifierVoiture':
                    $this->modifierVoiture($dVueEreur);
                    break;
            }

           
            header("Location: /siteSAE2A/voitures");
            exit;
        }

         
        $sousAction = $_GET['action'] ?? '';
         if ($sousAction === 'rechercherVoitures') {
              $this->rechercherVoitures($dVueEreur);
              return;
          }
        
        $results = $this->gateway->getAll();
        $this->afficherVue('flotte', $dVueEreur, $results,'admin');
    }
// ajouter les vue erreur et les vérif 
    

    private function ajouterVoiture(array $dVueEreur)
    {
        $modele    = $_POST['modele'] ?? '';
        $couleur   = $_POST['couleur'] ?? '';
        $puissance = $_POST['puissance'] ?? '';

        Validation::val_voiture($modele, $couleur, $puissance, $dVueEreur);

        if (empty($dVueEreur)) {
            $this->gateway->add( $modele, $couleur, $puissance);
            header("Location: /sitesae2A/voitures");
            exit;
        }

        $results = $this->gateway->getAll();
        $this->afficherVue('flotte', $dVueEreur, $results);
    }

    private function supprimerVoiture(array $dVueEreur)
    {
        $id = (int)($_POST['id'] ?? 0);       
        $this->gateway->delete($id);
        header("Location: /sitesae2A/voitures");
        exit;
    }

    private function modifierVoiture(array $dVueEreur)
    {
        $id        = (int)($_POST['id'] ?? 0);
        $modele    = $_POST['modele'] ?? '';
        $couleur   = $_POST['couleur'] ?? '';
        $puissance = $_POST['puissance'] ?? '';

        Validation::val_voiture($modele, $couleur, $puissance, $dVueEreur);

       if (empty($dVueEreur) && $id > 0) {

            $this->gateway->update($id, $modele, $couleur, $puissance);
            header("Location: /sitesae2A/voitures");
            exit;
        }
        $results = $this->gateway->getAll();
        $this->afficherVue('flotte', $dVueEreur, $results, 'admin');
    }

    private function rechercherVoitures(array $dVueErreur = []): void
{
    $motCle = trim($_GET['q'] ?? '');

    if ($motCle === '') {
        $results = $this->gateway->getAll();
        
    } else {
        $results = $this->gateway->rechercherVoitures($motCle);

        if (empty($results)) {
            $dVueErreur[] = "Aucune voiture trouvée pour \"$motCle\".";
        }
    }
    
    $this->afficherVue('flotte', $dVueErreur, $results, 'admin');
}


    public function inscription(array $dVueErreur)
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm'] ?? '';
        $role     = $_POST['role'] ?? '';

        
        Validation::val_user($username, $password, $confirm, $role, $dVueErreur);

        if (!empty($dVueErreur)) {
            require '../view/viewErreur.php';
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        
        $user = new User(null, $username, $hashedPassword, $role);
        $this->userGateway->login($user);

        
        $results = $this->gateway->getAll();
        $this->listeVoitures($dVueErreur);

        
    }

  
    public function connection(array $dVueErreur)
    {
        global $role;
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $savepass = $this->userGateway->getHashPass($username, $password);
        $role = $this->userGateway->getRole($username);
        Validation::val_connection($username, $password, $savepass, $dVueErreur);

        $role = $_POST['role'] ??'';
        //$dVueErreur='';
        if (!empty($dVueErreur)) {
            require '../view/viewErreur.php';
            exit;
        }        
        $results = $this->gateway->getAll();
        //$dVueErreur =array('');
        $this->listeVoitures($dVueErreur);


        
    }

    private function afficherVue(string $vueKey, array $dVueEreur, ?array $results = null, string $role = 'admin')
    {
        global $rep, $vues, $twig;
    
        
        if (!isset($vues[$vueKey])) {
            echo "Vue '$vueKey' non définie.";
            exit;
        }
    
        $vuePath = $vues[$vueKey];
    
        // twig 
        if (str_ends_with($vuePath, '.twig')) {
            echo $twig->render($vuePath, [
                'erreurs' => $dVueEreur,
                'results' => $results,
                'role'    => $role
            ]);
            return;
        }
    
        // php
        $cheminVue = realpath($rep . $vuePath);
        if ($cheminVue && file_exists($cheminVue)) {
            $resultsTwig = $results; 
            require($cheminVue);
        } else {
            echo "Fichier de vue introuvable : " . ($rep . $vuePath);
            exit;
        }
    }
    

    private function rechercherUtilisateur(array $dVueErreur = []): void
    {
        $motCle = trim($_GET['q'] ?? '');

        if ($motCle === '') {
            $results = $this->userGateway->getAllUser();
        } else {
            $results = $this->userGateway->rechercherUtilisateur($motCle);

            if (empty($results)) {
                $dVueErreur[] = "Aucun utilisateur trouvée pour \"$motCle\".";
            }
        }

        $this->afficherVue('user', $dVueErreur, $results, 'admin');
    }

    private function supprimerUtilisateur(array $dVueEreur)
    {
        $id = (int)($_POST['id'] ?? -1);
        if ($id >= 0) {
            $this->userGateway->deleteUser($id);
        }

        header("Location: /siteSAE2A/utilisateurs");
        exit;
    }

    private function ajouterUtilisateur(array $dVueEreur)
    {
        $username   = $_POST['username'] ?? '';
        $password   = $_POST['password'] ?? '';
        $confirm    = $_POST['confirm'] ?? '';
        $role = $_POST['role'] ?? '';

        Validation::val_user($username, $password, $confirm, $role, $dVueEreur);

        if (empty($dVueEreur)) {
            $this->userGateway->addUser( $username, $password, $role);
            header("Location: /siteSAE2A/utilisateurs");
            exit;

        }

        $results = $this->userGateway->getAllUser();
        $this->afficherVue('user', $dVueEreur, $results);
    }

    public function listeUtilisateur(array $dVueEreur)
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sousAction = $_POST['action'] ?? '';

            switch ($sousAction) {
                case 'ajouterUtilisateur':
                    $this->ajouterUtilisateur($dVueEreur);
                    break;

                case 'supprimerUtilisateur':
                    $this->supprimerUtilisateur($dVueEreur);
                    break;

                case 'modifierUtilisateur':
                    $this->modifierUtilisateur($dVueEreur);
                    break;

            }

            
            header("Location: /siteSAE2A/utilisateurs");
            exit;
        }

         
        $sousAction = $_GET['action'] ?? '';
         if ($sousAction === 'rechercherUtilisateur') {
              $this->rechercherUtilisateur($dVueEreur);
              return;
          }
        $results = $this->userGateway->getAllUser();
        $this->afficherVue('user', $dVueEreur, $results,'admin');
    }

    private function modifierUtilisateur(array $dVueEreur)
    {
        $username   = $_POST['username'] ?? '';
        $id = (int)($_POST['id'] ?? -1);

        

       if (empty($dVueEreur)) {

            $this->userGateway->update($username,$id);
            header("Location: /sitesae2A/utilisateurs");
            exit;
        }
        $results = $this->gateway->getAll();
        $this->afficherVue('user', $dVueEreur, $results, 'admin');
    }
}

?>