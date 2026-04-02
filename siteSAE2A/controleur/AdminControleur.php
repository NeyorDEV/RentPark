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
    private Client $apiClient;       // rentpark-api-pod (véhicules, contrats, rappels, users)
    private Client $clientApiClient; // rentpark-client-pod (clients)
    private Client $authApiClient;   // rentpark-auth-pod (authentification)
    private Client $contratApiClient; // rentpark-contrat-pod (contrats)
    private Client $planningApiClient; // rentpark-planning-pod (planning)
    private Client $rappelApiClient;  // rentpark-rappel-pod (rappels)
    private Client $userApiClient;    // rentpark-utilisateurs-pod (utilisateurs)

    public function __construct()
    {
        $this->checkAdmin();
        global $rep, $vues, $user, $pass, $dsn, $action;
        $dVueErreur = [];

        try {
            $this->connection         = new Connection($dsn, $user, $pass);
            $this->gateway            = new VehicleGateway($this->connection);
            $this->userGateway        = new UserGateway($this->connection);
            $this->reservationGateway = new ReservationGateway($this->connection);
            $this->apiClient          = getApiClient();
            $this->clientApiClient    = getClientApiClient();
            $this->authApiClient      = getAuthApiClient();
            $this->contratApiClient    = getContratApiClient();
            $this->planningApiClient   = getPlanningApiClient();
            $this->rappelApiClient     = getRappelApiClient();
            $this->userApiClient       = getUserApiClient();

            switch ($action) {
                case "afficheDashboard":  $this->afficheDashboard($dVueErreur);  break;
                case "listeUtilisateurs": $this->listeUtilisateurs($dVueErreur); break;
                case "listeClients":      $this->listeClients($dVueErreur);      break;
                default:
                    $dVueErreur[] = "Action inconnue";
                    $this->afficherVue('homeCustomers', $dVueErreur, null);
                    break;
            }
        } catch (\PDOException $e) {
            $dVueErreur[] = "Erreur BDD : " . $e->getMessage();
            $this->afficherVue('erreur', $dVueErreur, null);
        }
        exit(0);
    }

    private function authHeaders(): array
    {
        return [
            'headers' => [
                'Authorization' => 'Bearer ' . ($_SESSION['api_token'] ?? ''),
                'Content-Type'  => 'application/json',
            ]
        ];
    }

    private function withAuth(array $options = []): array
    {
        return array_merge_recursive($this->authHeaders(), $options);
    }

    public function afficheDashboard(array $dVueErreur)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sousAction = $_POST['action'] ?? '';
            if ($sousAction === 'ajouterRappel') {
                $this->ajouterRappel($dVueErreur);
            }
            header("Location: /siteSAE2A/dashboard");
            exit;
        }

        $results = [
            "revenusMensuels"  => $this->reservationGateway->getMonthlyIncome(),
            "voiturePlusLouee" => $this->gateway->getMostRentedCar(),
            "totalUsers"       => $this->userGateway->countUser()
        ];

        $alerts = [];

        $vehiculesCT = $this->gateway->getVehiculesControleTechniqueBientotExpire();
        foreach ($vehiculesCT as $v) {
            $alerts[] = [
                "label"    => "Contrôle technique",
                "vehicule" => $v["Marque"] . " " . $v["Modele"]
            ];
        }

        try {
            $responseRappels = $this->rappelApiClient->get('api/rappels', $this->authHeaders());
            $rappels = json_decode($responseRappels->getBody()->getContents(), true);
            if (is_array($rappels)) {
                foreach ($rappels as $rappel) {
                    $dateRappel = date('d/m/Y', strtotime($rappel['Date']));
                    $alerts[] = [
                        "label"    => "Rappel le $dateRappel : " . $rappel['Titre'],
                        "vehicule" => $rappel['Description']
                    ];
                }
            }
        } catch (RequestException $e) {
            // rappels indisponibles, on continue
        }

        $results["alerts"] = $alerts;

        $contrats = $this->reservationGateway->getContractsForNextMonth();
        $planning = [];
        $today    = date('Y-m-d');

        foreach ($contrats as $c) {
            if ($c['DateDebut'] >= $today) {
                $planning[] = [
                    'time'     => $c['DateDebut'],
                    'action'   => 'Location',
                    'vehicule' => $c['Marque'] . ' ' . $c['Modele']
                ];
            }
            if ($c['DateFin'] >= $today) {
                $planning[] = [
                    'time'     => $c['DateFin'],
                    'action'   => 'Retour',
                    'vehicule' => $c['Marque'] . ' ' . $c['Modele']
                ];
            }
        }

        usort($planning, fn($a, $b) => strcmp($a['time'], $b['time']));
        $results['planning'] = $planning;

        $this->afficherVue('dashboard', $dVueErreur, $results);
    }

    private function rechercherUtilisateur(array $dVueErreur)
    {
        $q = $_POST['q'] ?? '';
        $results = [];
        try {
            $response = $this->userApiClient->get('api/utilisateurs', $this->withAuth(['query' => ['q' => $q]]));
            $results = json_decode($response->getBody()->getContents(), true) ?? [];
        } catch (RequestException $e) {
            $dVueErreur[] = "Erreur recherche.";
        }
        $this->afficherVue('user', $dVueErreur, $results);
    }

    private function rechercherClient(array $dVueErreur = []): void
    {
        $motCle  = trim($_GET['q'] ?? '');
        $results = [];

        try {
            if ($motCle === '') {
                $response = $this->clientApiClient->get('api/clients', $this->authHeaders());
            } else {
                $response = $this->clientApiClient->get('api/clients', $this->withAuth([
                    'query' => ['q' => $motCle]
                ]));
            }
            $results = json_decode($response->getBody()->getContents(), true) ?? [];
            if (empty($results)) $dVueErreur[] = "Aucun client trouvé pour \"$motCle\".";
        } catch (RequestException $e) {
            $dVueErreur[] = "Erreur recherche client : " . $e->getMessage();
        }

        $this->afficherVue('client', $dVueErreur, $results);
    }

    private function ajouterClient(array $dVueErreur)
    {
        $data = [
            'Nom'         => $_POST['nom']         ?? '',
            'Prenom'      => $_POST['prenom']      ?? '',
            'Email'       => $_POST['email']       ?? '',
            'NumTel'      => $_POST['numTel']      ?? '',
            'NumPermis'   => $_POST['numPermis']   ?? '',
            'DateNaiss'   => $_POST['dateNaiss']   ?? '',
            'Nationalite' => $_POST['nationalite'] ?? '',
        ];

        if (empty($dVueErreur)) {
            try {
                $this->clientApiClient->post('api/clients', $this->withAuth(['json' => $data]));
                header("Location: /siteSAE2A/clients");
                exit;
            } catch (RequestException $e) {
                $dVueErreur[] = "Erreur ajout client : " . $e->getMessage();
            }
        }

        try {
            $response = $this->clientApiClient->get('api/clients', $this->authHeaders());
            $results  = json_decode($response->getBody()->getContents(), true) ?? [];
        } catch (RequestException $e) {
            $results = [];
        }
        $this->afficherVue('client', $dVueErreur, $results);
    }

    private function modifierClient(array $dVueErreur)
    {
        $IdClient = (int)($_POST['idClient'] ?? -1);
        $data = [
            'Nom'         => $_POST['nom']         ?? '',
            'Prenom'      => $_POST['prenom']      ?? '',
            'Email'       => $_POST['email']       ?? '',
            'NumTel'      => $_POST['numTel']      ?? '',
            'NumPermis'   => $_POST['numPermis']   ?? '',
            'DateNaiss'   => $_POST['dateNaiss']   ?? '',
            'Nationalite' => $_POST['nationalite'] ?? '',
        ];

        if (empty($dVueErreur)) {
            try {
                $this->clientApiClient->put("api/clients/$IdClient", $this->withAuth(['json' => $data]));
                header("Location: /siteSAE2A/clients");
                exit;
            } catch (RequestException $e) {
                $dVueErreur[] = "Erreur modification client : " . $e->getMessage();
            }
        }

        try {
            $response = $this->clientApiClient->get('api/clients', $this->authHeaders());
            $results  = json_decode($response->getBody()->getContents(), true) ?? [];
        } catch (RequestException $e) {
            $results = [];
        }
        $this->afficherVue('client', $dVueErreur, $results);
    }

    private function supprimerUtilisateur(array $dVueErreur)
    {
        $id = (int)($_POST['id'] ?? -1);
        if ($id <= 0) {
            header("Location: /siteSAE2A/utilisateurs");
            exit;
        }
        try {
            $this->userApiClient->delete("api/utilisateurs/$id", $this->authHeaders());
        } catch (RequestException $e) {
            $response = $this->userApiClient->get("api/utilisateurs", $this->authHeaders());
            $results = json_decode($response->getBody()->getContents(), true) ?? [];
            $this->afficherVue('user', $dVueErreur, $results);
        }
        header("Location: /siteSAE2A/utilisateurs");
        exit;
    }

    private function ajouterUtilisateur(array $dVueErreur)
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $role     = $_POST['role']     ?? '';

        Validation::val_user($username, $password, $role, $dVueErreur);

        if (empty($dVueErreur)) {
            try {
                $this->userApiClient->post("api/utilisateurs", $this->withAuth([
                    'json' => [
                        'username' => $username,
                        'password' => $password,
                        'role'     => $role
                    ]
                ]));
                header("Location: /siteSAE2A/utilisateurs");
                exit;
            } catch (RequestException $e) {
                $dVueErreur[] = "Erreur ajout utilisateur : " . $e->getMessage();
            }
        }

        $response = $this->userApiClient->get("api/utilisateurs", $this->authHeaders());
        $results = json_decode($response->getBody()->getContents(), true) ?? [];
        $this->afficherVue('user', $dVueErreur, $results);
    }

    public function listeUtilisateurs(array $dVueErreur)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sousAction = $_POST['action'] ?? '';
            switch ($sousAction) {
                case 'ajouterUtilisateur':
                    $this->ajouterUtilisateur($dVueErreur);
                    break;
                case 'supprimerUtilisateur':
                    $this->supprimerUtilisateur($dVueErreur);
                    break;
                case 'modifierUtilisateur':
                    $this->modifierUtilisateur($dVueErreur);
                    break;
                case 'rechercherUtilisateur':
                    $this->rechercherUtilisateur($dVueErreur);
                    return;
            }
            header("Location: /siteSAE2A/utilisateurs");
            exit;
        }

        $results = [];
        try {
            $response = $this->userApiClient->get('api/utilisateurs', $this->authHeaders());
            $results = json_decode($response->getBody()->getContents(), true) ?? [];
        } catch (RequestException $e) {
            $dVueErreur[] = "Erreur service utilisateurs.";
        }
        $this->afficherVue('user', $dVueErreur, $results);
    }

    public function listeClients(array $dVueErreur)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sousAction = $_POST['action'] ?? '';
            switch ($sousAction) {
                case 'ajouterClient':  $this->ajouterClient($dVueErreur);  break;
                case 'modifierClient': $this->modifierClient($dVueErreur); break;
            }
            header("Location: /siteSAE2A/clients");
            exit;
        }

        $sousAction = $_GET['action'] ?? '';
        if ($sousAction === 'rechercherClient') {
            $this->rechercherClient($dVueErreur);
            return;
        }

        try {
            $response = $this->clientApiClient->get('api/clients', $this->authHeaders());
            $results  = json_decode($response->getBody()->getContents(), true) ?? [];
        } catch (RequestException $e) {
            $dVueErreur[] = "Erreur chargement clients : " . $e->getMessage();
            $results = [];
        }
        $this->afficherVue('client', $dVueErreur, $results);
    }

    private function modifierUtilisateur(array $dVueErreur)
    {
        $id = $_POST['id'] ?? '';
        $username = $_POST['username'] ?? '';
        try {
            $this->userApiClient->put("api/utilisateurs/$id", $this->withAuth([
                'json' => ['username' => $username]
            ]));
        } catch (RequestException $e) {
            $response = $this->userApiClient->get("api/utilisateurs", $this->authHeaders());
            $results = json_decode($response->getBody()->getContents(), true) ?? [];
            $this->afficherVue('user', $dVueErreur, $results);
        }
    }

    private function ajouterRappel(array &$dVueErreur)
    {
        try {
            $this->rappelApiClient->post('api/rappels', $this->withAuth([
                'json' => [
                    'Titre'       => $_POST['Titre']       ?? '',
                    'Description' => $_POST['Description'] ?? '',
                    'Date'        => $_POST['Date']        ?? '',
                ]
            ]));
        } catch (RequestException $e) {
            $dVueErreur[] = "Erreur ajout rappel : " . $e->getMessage();
        }
    }
}