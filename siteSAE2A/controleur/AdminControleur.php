<?php
namespace controleur;
use modele\Connection;
use modele\UserGateway;
use modele\VehicleGateway;
use modele\ReservationGateway;
use modele\User;
use config\Validation;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

require_once __DIR__ . '/ApiHelper.php';
class AdminControleur
{
    use RoleAwareTrait;
    private Connection $connection;
    private VehicleGateway $gateway;
    private UserGateway $userGateway;
    private ReservationGateway $reservationGateway;

    // test pour l'API3
    private Client $apiClient;

    public function __construct()
    {
        $this->checkAdmin();

        global $rep, $vues, $user, $pass, $dsn, $action;


        $dVueEreur = [];

        try {

            $this->connection = new Connection($dsn, $user, $pass);
            $this->gateway = new VehicleGateway($this->connection);
            $this->userGateway = new UserGateway($this->connection);
            $this->reservationGateway = new ReservationGateway($this->connection);

            // test pour l'API
            $this->apiClient = getApiClient();


            switch ($action) {
                case "afficheDashboard":
                    $this->afficheDashboard($dVueEreur);
                    break;
                case "listeUtilisateurs":
                    $this->listeUtilisateurs($dVueEreur);
                    break;
                case "listeClients":
                    $this->listeClients($dVueEreur);
                    break;
                default:
                    $dVueEreur[] = "Action inconnue";
                    $this->afficherVue('homeCustomers', $dVueEreur, $results = null);
                    break;
            }

        } catch (\PDOException $e) {
            $dVueEreur[] = "Erreur BDD : " . $e->getMessage();
            $this->afficherVue('erreur', $dVueEreur, $results = null);
        }

        exit(0);
    }
    
    // ajouter les vue erreur et les vérif 

    public function afficheDashboard(array $dVueEreur)
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sousAction = $_POST['action'] ?? '';

            if ($sousAction === 'ajouterRappel') {
                $this->ajouterRappel($dVueEreur);
            }

            header("Location: /siteSAE2A/dashboard");
            exit;
        }

        $results = [
            "revenusMensuels" => $this->reservationGateway->getMonthlyIncome(),
            "voiturePlusLouee" => $this->gateway->getMostRentedCar(),
            "totalUsers" => $this->userGateway->countUser()
        ];

        $alerts = [];

        // Contrôle technique < 2 mois
        $vehiculesCT = $this->gateway->getVehiculesControleTechniqueBientotExpire();

        foreach ($vehiculesCT as $v) {
            $alerts[] = [
                "label" => "Contrôle technique",
                "vehicule" => $v["Marque"] . " " . $v["Modele"]
            ];
        }

        try {
            $responseRappels = $this->apiClient->get('rappels');
            $rappels = json_decode($responseRappels->getBody()->getContents(), true);

            if (is_array($rappels)) {
                foreach ($rappels as $rappel) {
                    $dateRappel = date('d/m/Y', strtotime($rappel['Date']));
                    $alerts[] = [
                        "label" => "Rappel le $dateRappel : " . $rappel['Titre'],
                        "vehicule" => $rappel['Description']
                    ];
                }
            }
        } catch (RequestException $e) {
        }

        // On injecte les alertes dans les résultats
        $results["alerts"] = $alerts;

        $contrats = $this->reservationGateway->getContractsForNextMonth();
        $planning = [];
        $today = date('Y-m-d'); // date du jour

        foreach ($contrats as $c) {
            // Départ (date de début) uniquement si futur ou aujourd'hui
            if ($c['DateDebut'] >= $today) {
                $planning[] = [
                    'time' => $c['DateDebut'],
                    'action' => 'Location',
                    'vehicule' => $c['Marque'] . ' ' . $c['Modele']
                ];
            }

            // Arrivée (date de fin) uniquement si futur ou aujourd'hui
            if ($c['DateFin'] >= $today) {
                $planning[] = [
                    'time' => $c['DateFin'],
                    'action' => 'Retour',
                    'vehicule' => $c['Marque'] . ' ' . $c['Modele']
                ];
            }
        }

        // Trier par date
        usort($planning, fn($a, $b) => strcmp($a['time'], $b['time']));

        $results['planning'] = $planning;




        // Tri chronologique
        usort($planning, fn($a, $b) => strcmp($a['time'], $b['time']));

        $this->afficherVue('dashboard', $dVueEreur, $results);
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

        $this->afficherVue('user', $dVueErreur, $results);
    }
    private function rechercherClient(array $dVueErreur = []): void
    {
        //A faire
        $motCle = trim($_GET['q'] ?? '');
        $results = [];

        if ($motCle === '') {
            //A faire
        } else {
            // A faire

            if (empty($results)) {
                $dVueErreur[] = "Aucun utilisateur trouvée pour \"$motCle\".";
            }
        }

        $this->afficherVue('client', $dVueErreur, $results);
    }
    private function modifierClient(array $dVueEreur)
    {
        
        $Nom = $_POST['nom'] ?? '';
        $Prenom = $_POST['prenom'] ?? '';
        $Email = $_POST['email'] ?? '';
        $NumTel = $_POST['numTel'] ?? '';
        $NumPermis = $_POST['numPermis'] ?? '';
        $DateNaiss = $_POST['dateNaiss'] ?? '';
        $Nationalite = $_POST['nationalite'] ?? '';
        $IdClient = (int) ($_POST['idClient'] ?? -1);

        //Validation::val_client($Nom, $Prenom, $Email, $NumTel, $NumPermis, $DateNaiss, $Nationalite, $IdClient, $dVueEreur);

        if (empty($dVueEreur)) {
            $this->apiClient->put("client/$IdClient", [
                'json' => [
                    'Nom' => $Nom,
                    'Prenom' => $Prenom,
                    'Email' => $Email,
                    'NumTel' => $NumTel,
                    'NumPermis' => $NumPermis,
                    'DateNaiss' => $DateNaiss,
                    'Nationalite' => $Nationalite
                ]
            ]);
            
            header("Location: /siteSAE2A/clients");
            exit;
        }
        $response = $this->apiClient->get('clients');
        $results = json_decode($response->getBody()->getContents(), true);
        $this->afficherVue('client', $dVueEreur, $results);
    }
    // ok utilise API
    private function supprimerUtilisateur(array $dVueEreur)
    {
        $id = (int) ($_POST['id'] ?? -1);

        if ($id <= 0) {
            header("Location: /siteSAE2A/utilisateurs");
            exit;
        }

        try {
            // Appel API DELETE
            $this->apiClient->delete("users/$id");

        } catch (RequestException $e) {
            $dVueEreur[] = "Erreur lors de la suppression via l’API.";
            // Optionnel : log
            // error_log($e->getMessage());
        }

        header("Location: /siteSAE2A/utilisateurs");
        exit;
    }

    // ok utilise API
    private function ajouterUtilisateur(array $dVueEreur)
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? '';

        Validation::val_user($username, $password, $role, $dVueEreur);

        if (empty($dVueEreur)) {
            $this->apiClient->post("/add/users", [
                'form_params' => [
                    'username' => $username,
                    'password' => $password,
                    'role' => $role
                ]
            ]);
            header("Location: /siteSAE2A/utilisateurs");
            exit;

        }

        $results = $this->apiClient->get("users");
        $results = json_decode($results->getBody()->getContents(), true);
        $this->afficherVue('user', $dVueEreur, $results);
    }

    private function ajouterClient(array $dVueEreur)
    {
        $Nom = $_POST['nom'] ?? '';
        $Prenom = $_POST['prenom'] ?? '';
        $Email = $_POST['email'] ?? '';
        $NumTel = $_POST['numTel'] ?? '';
        $NumPermis = $_POST['numPermis'] ?? '';
        $DateNaiss = $_POST['dateNaiss'] ?? '';
        $Nationalite = $_POST['nationalite'] ?? '';
        

        //Validation::val_client($Nom, $Prenom, $Email, $NumTel, $NumPermis, $DateNaiss, $Nationalite, $IdClient, $dVueEreur);

        if (empty($dVueEreur)) {
            $this->apiClient->post("client", [
                'json' => [
                    'Nom' => $Nom,
                    'Prenom' => $Prenom,
                    'Email' => $Email,
                    'NumTel' => $NumTel,
                    'NumPermis' => $NumPermis,
                    'DateNaiss' => $DateNaiss,
                    'Nationalite' => $Nationalite
                ]
            ]);
            header("Location: /siteSAE2A/clients");
            exit;

        }

        $response = $this->apiClient->get('clients');
        $results = json_decode($response->getBody()->getContents(), true);

        $this->afficherVue('client', $dVueEreur, $results);
    }

    

    public function listeUtilisateurs(array $dVueEreur)
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
        $this->afficherVue('user', $dVueEreur, $results);
    }

    public function listeClients(array $dVueEreur)
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sousAction = $_POST['action'] ?? '';

            switch ($sousAction) {
                case 'ajouterClient':
                    $this->ajouterClient($dVueEreur);
                    break;
                case 'modifierClient':
                    $this->modifierClient($dVueEreur);
                    break;

            }
            header("Location: /siteSAE2A/clients");
            exit;
        }

        $sousAction = $_GET['action'] ?? '';
        if ($sousAction === 'rechercherClient') {
            $this->rechercherClient($dVueEreur);
            return;
        }
        $response = $this->apiClient->get('clients');
        $results = json_decode($response->getBody()->getContents(), true);
        $this->afficherVue('client', $dVueEreur, $results);
    }
  

    // ------------------------------------------------------------------------------------------------------
    private function modifierUtilisateur(array $dVueEreur)
    {
        $username = $_POST['username'] ?? '';
        $id = (int) ($_POST['id'] ?? -1);
        $role= $_POST['role'] ?? '';

        // faire de quoi changer le rôle pour le super admin mais pas pour les employés
        if (empty($dVueEreur)) {
            
            $this->userGateway->update($username, $id, );
            header("Location: /sitesae2A/utilisateurs");
            exit;
        }
        $results = $this->gateway->getAll();
        $this->afficherVue('user', $dVueEreur, $results);
    }
    private function ajouterRappel(array &$dVueEreur)
    {
        $titre = $_POST['Titre'] ?? '';
        $description = $_POST['Description'] ?? '';
        $date = $_POST['Date'] ?? '';

        try {
            $this->apiClient->post('rappel', [
                'json' => [
                    'Titre' => $titre,
                    'Description' => $description,
                    'Date' => $date
                ]
            ]);
        } catch (RequestException $e) {
            $dVueEreur[] = "Erreur lors de l'ajout du rappel personnalisé.";
        }
    }
}
?>