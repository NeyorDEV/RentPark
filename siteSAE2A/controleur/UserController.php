<?php
namespace controleur;
use modele\Connection;
use modele\VehicleGateway;
use modele\UserGateway;
use GuzzleHttp\Client;
use config\Validation;
use modele\User;

class UserController
{
    private Connection $connection;
    private VehicleGateway $gateway;

    private UserGateway $userGateway;

    private Client $apiClient;

    public function __construct()
    {
        global $rep, $vues, $user, $pass, $dsn, $action;

        $dVueErreur = [];

        try {
            $this->connection = new Connection($dsn, $user, $pass);
            $this->gateway = new VehicleGateway($this->connection);
            $this->userGateway = new UserGateway($this->connection);

            $this->apiClient = new Client([
                'base_uri' => 'http://localhost:8880/',
                'timeout' => 2.0
            ]);


            switch ($action) {
                case "afficheInscription":
                    $this->afficheInscription($dVueErreur);
                    break;
                case "afficheConnection":
                    $this->afficheConnection($dVueErreur);
                    break;
                case 'deconnecter':
                    $this->deconnecter();
                    break;
                case 'cars':
                        $this->cars($dVueErreur);
                        break;
                case 'homeCustomers':
                    $this->homeCustomers($dVueErreur);
                    break;
                default:
                    $dVueEreur[] = "Action inconnue";
                    $this->afficherVue('homeCustomers', $dVueEreur, $results = null, 'user');
                    break;
            }

        } catch (\PDOException $e) {
            $dVueErreur[] = "Erreur BDD : " . $e->getMessage();
            $this->afficherVue('erreur', $dVueErreur);
        }

        exit(0);
    }


    public function homeCustomers(array $dVueEreur)
    {

        $this->afficherVue('homeCustomers', $dVueEreur, $results = null, 'user');

    }

    public function afficheInscription(array $dVueEreur)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sousAction = $_POST['action'] ?? '';

            switch ($sousAction) {
                case 'inscription':
                    $this->inscription($dVueEreur);
                    break;
            }


            header("Location: /siteSAE2A/connection");
            exit;
        }
        $this->afficherVue('inscription', $dVueEreur, $results = null, 'user');

    }

    public function deconnecter(): void
    {
        session_unset();
        session_destroy();
        header("Location: /siteSAE2A/connection");
        exit;
    }

    public function inscription(array $dVueErreur)
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';
        $role = $_POST['role'] ?? '';


        Validation::val_user($username, $password, $confirm, $role, $dVueErreur);

        if (!empty($dVueErreur)) {
            $dVueErreur[] = "erreur dans l'inscription";
            $this->afficherVue('erreur', $dVueErreur, $results = null, 'user');
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


        $user = new User(null, $username, $hashedPassword, $role);
        $this->userGateway->login($user);


    }

    public function afficheConnection(array $dVueEreur)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sousAction = $_POST['action'] ?? '';

            switch ($sousAction) {
                case 'connection':
                    $this->connection($dVueEreur);
                    break;
            }


            header("Location: /siteSAE2A/voitures");
            exit;
        }
        $this->afficherVue('connection', $dVueEreur, $results = null, 'user');

    }

    public function connection(array $dVueErreur)
    {
        global $role;
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $savepass = $this->userGateway->getHashPass($username, $password);
        $role = $this->userGateway->getRole($username);
        Validation::val_connection($username, $password, $savepass, $dVueErreur);


        session_regenerate_id(true);
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;

        


        if (!empty($dVueErreur)) {
            $this->afficherVue('erreur', $dVueErreur, $results = null, 'user');
            exit;
        }

    }

    public function cars(array $dVueEreur)
{
    // 1. Récupération des filtres depuis l'URL (GET)
    $date_depart = $_GET['date_depart'] ?? null;
    $date_retour = $_GET['date_retour'] ?? null;
    $boite_filtre = $_GET['boite'] ?? null;
    // AJOUT : Récupération du filtre énergie
    $energie_filtre = $_GET['energie'] ?? null; 
    
    $prix_min = isset($_GET['prix_min']) && $_GET['prix_min'] !== '' ? (float)$_GET['prix_min'] : null;
    $prix_max = isset($_GET['prix_max']) && $_GET['prix_max'] !== '' ? (float)$_GET['prix_max'] : null;

    // 2. Appel de l'API
    try {
        $response = $this->apiClient->get('voitures');
        $results = json_decode($response->getBody()->getContents(), true);
    } catch (RequestException $e) {
        $dVueEreur[] = "Impossible de récupérer les véhicules depuis l’API.";
        $results = [];
    }

    // 3. Application des filtres PHP
    if (!empty($results)) {
        $results = array_filter($results, function($voiture) use ($boite_filtre, $energie_filtre, $prix_min, $prix_max) {
            $match = true;
            $prixVoiture = isset($voiture['Prix']) ? (float)$voiture['Prix'] : 0;

            if ($boite_filtre && (!isset($voiture['Boite']) || $voiture['Boite'] !== $boite_filtre)) {
                $match = false;
            }
            
            // AJOUT : Logique de filtrage pour l'énergie
            if ($match && $energie_filtre && (!isset($voiture['Energie']) || $voiture['Energie'] !== $energie_filtre)) {
                $match = false;
            }

            if ($match && $prix_min !== null && $prixVoiture < $prix_min) {
                $match = false;
            }
            if ($match && $prix_max !== null && $prixVoiture > $prix_max) {
                $match = false;
            }
            return $match;
        });
        
        $results = array_values($results);
    }

    $this->afficherVue('cars', $dVueEreur, $results, 'user');
}


    private function afficherVue(string $vueKey, array $dVueEreur, ?array $results = null, string $role = 'user')
    {
        global $rep, $vues;
        if (!isset($vues[$vueKey])) {
            echo "Vue '$vueKey' non définie.";
            exit;
        }
        $cheminVue = realpath($rep . $vues[$vueKey]);
        if ($cheminVue && file_exists($cheminVue)) {
            require_once($cheminVue); // NOSONAR
        } else {
            echo "Fichier de vue introuvable : " . ($rep . $vues[$vueKey]);
            exit;
        }
    }
}
?>
