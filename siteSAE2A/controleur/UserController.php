<?php
namespace controleur;

use modele\Connection;
use modele\VehicleGateway;
use modele\UserGateway;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use config\Validation;
use modele\User;

require_once __DIR__ . '/ApiHelper.php';

class UserController
{
    use RoleAwareTrait;

    private Connection $connection;
    private VehicleGateway $gateway;
    private UserGateway $userGateway;
    private Client $apiClient;

    public function __construct()
    {
        global $user, $pass, $dsn;

        try {
            $this->connection = new Connection($dsn, $user, $pass);
            $this->gateway = new VehicleGateway($this->connection);
            $this->userGateway = new UserGateway($this->connection);
            $this->apiClient = getApiClient();
        } catch (\PDOException $e) {
            $dVueErreur[] = "Erreur BDD : " . $e->getMessage();
            $this->afficherVue('erreur', $dVueErreur);
            exit(0);
        }
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

    public function homeCustomers()
    {
        $dVueErreur = [];
        $this->afficherVue('homeCustomers', $dVueErreur, null);
    }

    public function afficheInscription()
    {
        $dVueErreur = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->inscription($dVueErreur);
            return;
        }
        $this->afficherVue('inscription', $dVueErreur, null);
    }

    private function inscription(array &$dVueErreur)
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm'] ?? '';
        $role     = $_POST['role'] ?? 'user';

        Validation::val_user($username, $password, $confirm, $dVueErreur);

        if (!empty($dVueErreur)) {
            $this->afficherVue('erreur', $dVueErreur, null);
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $userObj = new User(null, $username, $hashedPassword, $role);
        $this->userGateway->login($userObj);
        
        header("Location: /siteSAE2A/connection");
        exit;
    }

    public function afficheConnection()
    {
        $dVueErreur = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traitementConnection($dVueErreur);
            return;
        }
        $this->afficherVue('connection', $dVueErreur, null);
    }

    private function traitementConnection(array &$dVueErreur)
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // 1. Validation BDD locale
        $savepass = $this->userGateway->getHashPass($username, $password);
        Validation::val_connection($username, $password, $savepass, $dVueErreur);

        if (!empty($dVueErreur)) {
            $this->afficherVue('erreur', $dVueErreur, null);
            return;
        }

        // 2. Appel de l'API avec Guzzle
        try {
            $client = new Client([
                'base_uri' => 'https://codefirst.iut.uca.fr/kubernetes/iut-inf63-projets-etudiants-rentpark/rentpark-auth-pod/',
                'timeout'  => 60.0,
                'verify'   => false,
                'http_errors' => true,
            ]);
            
            $response = $client->post('login', [
                'json' => ['username' => $username, 'password' => $password]
            ]);
            
            $data = json_decode($response->getBody()->getContents(), true);
            
            // 3. Création de la session sécurisée
            session_regenerate_id(true);
            $_SESSION['username']  = $username;
            $_SESSION['role']      = $data['user']['role'] ?? $this->userGateway->getRole($username);
            $_SESSION['idClient']  = $this->userGateway->getClientId($username);
            $_SESSION['api_token'] = $data['token']; // Le fameux token JWT
            
            // 4. Redirection vers l'accueil après le succès
            header("Location: /siteSAE2A/home");
            exit;
            
        } catch (\Exception $e) { 
            $dVueErreur[] = "Erreur de connexion à l'API : " . $e->getMessage();
            $this->afficherVue('erreur', $dVueErreur, null);
            return;
        }
    }

    public function deconnecter()
    {
        session_unset();
        session_destroy();
        header("Location: /siteSAE2A/home");
        exit;
    }

    public function cars()
    {
        $dVueErreur = [];
        $date_depart  = $_GET['date_depart'] ?? null;
        $date_retour  = $_GET['date_retour'] ?? null;
        $boite_filtre = $_GET['boite'] ?? null;
        $energie_filtre = $_GET['energie'] ?? null;
        $prix_min = isset($_GET['prix_min']) && $_GET['prix_min'] !== '' ? (float)$_GET['prix_min'] : null;
        $prix_max = isset($_GET['prix_max']) && $_GET['prix_max'] !== '' ? (float)$_GET['prix_max'] : null;

        try {
            $response = $this->apiClient->get('voituresForReservation', $this->withAuth([
                'query' => [
                    'date_depart' => $date_depart,
                    'date_retour' => $date_retour,
                ]
            ]));
            $results = json_decode($response->getBody()->getContents(), true) ?? [];
        } catch (RequestException $e) {
            $dVueErreur[] = "Impossible de récupérer les véhicules depuis l'API.";
            $results = [];
        }

        if (!empty($results)) {
            $results = array_values(array_filter($results, function($voiture) use ($boite_filtre, $energie_filtre, $prix_min, $prix_max) {
                $prixVoiture = isset($voiture['Prix']) ? (float)$voiture['Prix'] : 0;
                if ($boite_filtre   && ($voiture['Boite']   ?? '') !== $boite_filtre)   return false;
                if ($energie_filtre && ($voiture['Energie'] ?? '') !== $energie_filtre) return false;
                if ($prix_min !== null && $prixVoiture < $prix_min) return false;
                if ($prix_max !== null && $prixVoiture > $prix_max) return false;
                return true;
            }));
        }

        $this->afficherVue('cars', $dVueErreur, $results);
    }

    public function reservationForm()
    {
        $dVueErreur = [];
        $this->afficherVue('reservationForm', $dVueErreur, null);
    }

    public function afficheRecapitulatif()
    {
        $dVueErreur = [];
        $this->afficherVue('recapitulatif', $dVueErreur, null);
    }

    public function finaliserReservation()
    {
        $dVueErreur = [];
        try {
            $nom        = $_POST['nom']        ?? null;
            $prenom     = $_POST['prenom']     ?? null;
            $email      = $_POST['email']      ?? null;
            $tel        = $_POST['numTel']     ?? null;
            $permis     = $_POST['numPermis']  ?? null;
            $dateNaiss  = $_POST['datenaiss']  ?? null;
            $nationalite = $_POST['nationalite'] ?? null;
            $numSerie   = $_POST['num_serie']  ?? null;
            $dateDebut  = $_POST['date_debut'] ?? null;
            $dateFin    = $_POST['date_fin']   ?? null;

            $dateN = new \DateTime($dateNaiss);
            $age   = (new \DateTime())->diff($dateN)->y;
            if ($age < 18) throw new \Exception("Vous devez avoir au moins 18 ans pour réserver.");

            $debut = new \DateTime($dateDebut);
            $fin   = new \DateTime($dateFin);
            $now   = new \DateTime('today');
            if ($debut < $now)   throw new \Exception("La date de départ ne peut pas être dans le passé.");
            if ($fin   < $debut) throw new \Exception("La date de retour doit être égale ou supérieure à la date de départ.");

            $responseVehicule = $this->apiClient->get("vehicules/$numSerie", $this->authHeaders());
            $vehicule = json_decode($responseVehicule->getBody()->getContents(), true);
            if (!$vehicule) throw new \Exception("Véhicule introuvable pour le contrat.");

            try {
                $responseClient = $this->apiClient->post('client', $this->withAuth([
                    'json' => [
                        'Nom'         => $nom,
                        'Prenom'      => $prenom,
                        'Email'       => $email,
                        'NumTel'      => $tel,
                        'NumPermis'   => $permis,
                        'DateNaiss'   => $dateNaiss,
                        'Nationalite' => $nationalite
                    ]
                ]));
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    $body = $e->getResponse()->getBody()->getContents();
                    $data = json_decode($body, true);
                    if ($e->getResponse()->getStatusCode() === 409) {
                        $dVueErreur[] = "Conflit d'identité : Email ou Permis déjà utilisés.";
                        $dVueErreur[] = $data['message'] ?? "Erreur de doublon.";
                    } else {
                        $dVueErreur[] = "Erreur API client.";
                        $dVueErreur[] = $e->getMessage();
                    }
                } else {
                    $dVueErreur[] = "Impossible de contacter le serveur.";
                }
                $this->afficherVue('erreur', $dVueErreur, null);
                return;
            }

            $dataClient = json_decode($responseClient->getBody()->getContents(), true);
            $idClient   = $dataClient['idClient'] ?? null;

            if ($idClient) {
                $responseContrat = $this->apiClient->post('modif/contrat', $this->withAuth([
                    'json' => [
                        'DateDebut'   => $dateDebut,
                        'DateFin'     => $dateFin,
                        'Statut'      => 'EnCoursValidation',
                        'IdClient'    => (int)$idClient,
                        'EtatAvant'   => 5,
                        'IdVehicule'  => $numSerie,
                        'Marque'      => $vehicule['Marque'],
                        'NomModele'   => $vehicule['Nom'],
                        'AnneeModele' => $vehicule['Annee']
                    ]
                ]));
                $dataContrat = json_decode($responseContrat->getBody()->getContents(), true);
                if (isset($dataContrat['message']) && $dataContrat['message'] === 'Contrat créé avec succès') {
                    $this->afficherVue('confirmationSucces', $dVueErreur);
                } else {
                    $dVueErreur[] = "Erreur lors de la création du contrat.";
                    $this->afficherVue('erreur', $dVueErreur, null);
                }
            } else {
                $dVueErreur[] = "Erreur lors de la création du client.";
                $this->afficherVue('erreur', $dVueErreur, null);
            }

        } catch (RequestException $e) {
            $dVueErreur[] = "Erreur API : " . $e->getMessage();
            $this->afficherVue('erreur', $dVueErreur, null);
        } catch (\Exception $e) {
            $dVueErreur[] = "Erreur technique : " . $e->getMessage();
            $this->afficherVue('erreur', $dVueErreur, null);
        }
    }

    public function listeReservation()
    {
        $dVueErreur = ['PAGE non faite pour user'];
        $this->afficherVue('erreur', $dVueErreur, []);
    }

    public function listeVoitures()
    {
        $dVueErreur = [];
        $sousAction = $_GET['action'] ?? '';
        
        if ($sousAction === 'rechercherVoitures') {
            $this->rechercherVoitures($dVueErreur);
            return;
        }
        
        try {
            $response = $this->apiClient->get('api/vehicules', $this->authHeaders());
            $results  = json_decode($response->getBody()->getContents(), true) ?? [];
        } catch (RequestException $e) {
            $dVueErreur[] = "Impossible de récupérer les véhicules depuis l'API.";
            $results = [];
        }
        $this->afficherVue('flotte', $dVueErreur, $results);
    }

    private function rechercherVoitures(array &$dVueErreur): void
    {
        $motCle = trim($_GET['q'] ?? '');
        try {
            $options = $motCle !== '' ? $this->withAuth(['query' => ['nom' => $motCle]]) : $this->authHeaders();
            $response = $this->apiClient->get('api/vehicules', $options);
            $results  = json_decode($response->getBody()->getContents(), true) ?? [];
            if (empty($results)) $dVueErreur[] = "Aucune voiture trouvée pour \"$motCle\".";
        } catch (\Exception $e) {
            $dVueErreur[] = "Erreur recherche : " . $e->getMessage();
            $results = [];
        }
        $this->afficherVue('flotte', $dVueErreur, $results);
    }

    public function afficheParametres()
    {
        $dVueErreur = [];
        $this->afficherVue('parametres', $dVueErreur, null);
    }
}