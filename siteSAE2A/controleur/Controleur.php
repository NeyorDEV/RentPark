<?php
namespace controleur;
use modele\Connection;
use modele\UserGateway;
use modele\VehicleGateway;
use modele\User;
use config\Validation;
class Controleur
{
    private Connection $connection;
    private VehicleGateway $gateway;

    private UserGateway $userGateway;

    public function __construct()
    {
        global $rep, $vues, $user, $pass, $dsn;
        session_start();

        $dVueEreur = [];

        try {
            // ⚡ Instanciation de la connexion
            $this->connection = new Connection($dsn, $user, $pass);

            // ⚡ Instanciation de la Gateway
            $this->gateway = new VehicleGateway($this->connection);
            $this->userGateway = new UserGateway($this->connection);

            // Action
            $action = $_REQUEST['action'] ?? null;

            switch ($action) {
                case null:
                    $this->listeVoitures($dVueEreur);
                    break;

                case "ajouterVoiture":
                    $this->ajouterVoiture($dVueEreur);
                    break;

                case "supprimerVoiture":
                    $this->supprimerVoiture($dVueEreur);
                    break;

                case "inscription":
                    $this->inscription( $dVueEreur);
                    break;

                default:
                    $dVueEreur[] = "Action inconnue";
                    $this->afficherVue('flotte', $dVueEreur,$results=null,'admin');
                    break;
            }

        } catch (\PDOException $e) {
            $dVueEreur[] = "Erreur BDD : " . $e->getMessage();
            $this->afficherVue('erreur', $dVueEreur, $results = null,'admin');
        }

        exit(0);
    }

    private function listeVoitures(array $dVueEreur)
    {
        $results = $this->gateway->getAll();
        $this->afficherVue('flotte', $dVueEreur, $results,'admin');
    }

    private function ajouterVoiture(array $dVueEreur)
    {
        $modele    = $_POST['modele'] ?? '';
        $couleur   = $_POST['couleur'] ?? '';
        $puissance = $_POST['puissance'] ?? '';

        Validation::val_voiture($modele, $couleur, $puissance, $dVueEreur);

        if (empty($dVueEreur)) {
            $this->gateway->add(0, $modele, $couleur, $puissance);
            header("Location: index.php");
            exit;
        }

        $results = $this->gateway->getAll();
        $this->afficherVue('flotte', $dVueEreur, $results);
    }

    private function supprimerVoiture(array $dVueEreur)
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id >= 0) {             // à revoir plus tard 
            $this->gateway->delete($id);
        }

        header("Location: index.php");
        exit;
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
        $this->userGateway->addUser($user);

        
        $results = $this->gateway->getAll();
        require __DIR__ . '/../view/viewVoiture.php';

        
    }

  
    private function afficherVue(string $vueKey, array $dVueEreur, array  $results=null,string $role ='admin' )
    {
        global $rep, $vues;

        if (!isset($vues[$vueKey])) {
            echo "Vue '$vueKey' non définie.";
            exit;
        }

        $cheminVue = realpath($rep . $vues[$vueKey]);

        if ($cheminVue && file_exists($cheminVue)) {
            require($cheminVue);
        } else {
            echo "Fichier de vue introuvable : " . ($rep . $vues[$vueKey]);
            exit;
        }
    }
}
?>