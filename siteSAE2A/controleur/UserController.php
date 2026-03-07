<?php
namespace controleur;
use modele\Connection;
use modele\VehicleGateway;
use modele\UserGateway;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use config\Validation;
use modele\User;

class UserController
{
    use RoleAwareTrait;
    
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
                case "listeVoitures":
                    $this->listeVoitures($dVueErreur);
                    break;
                case 'cars':
                    $this->cars($dVueErreur);
                    break;
                case 'homeCustomers':
                    $this->homeCustomers($dVueErreur);
                    break;
                case 'reservationForm':
                    $this->reservationForm($dVueErreur);
                    break;
                case 'afficheRecapitulatif':
                    $this->afficheRecapitulatif($dVueErreur);
                    break;
                case 'finaliserReservation':
                    $this->finaliserReservation($dVueErreur);
                    break;
                case 'listeReservation':
                    $this->listeReservation($dVueErreur);
                    break;
                case 'afficheParametres':
                    $this->afficheParametres($dVueErreur);
                    break;
                default:
                    $dVueErreur[] = "Action inconnue";
                    $this->afficherVue('homeCustomers', $dVueErreur, $results = null);
                    break;
            }

        } catch (\PDOException $e) {
            $dVueErreur[] = "Erreur BDD : " . $e->getMessage();
            $this->afficherVue('erreur', $dVueErreur);
        }

        exit(0);
    }

    public function homeCustomers(array $dVueErreur)
    {

        $this->afficherVue('homeCustomers', $dVueErreur, $results = null);

    }

    public function afficheInscription(array $dVueErreur)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sousAction = $_POST['action'] ?? '';

            switch ($sousAction) {
                case 'inscription':
                    $this->inscription($dVueErreur);
                    break;
            }


            header("Location: /siteSAE2A/connection");
            exit;
        }
        $this->afficherVue('inscription', $dVueErreur, $results = null);

    }

    public function deconnecter(): void
    {
        session_unset();
        session_destroy();
        header("Location: /siteSAE2A/home");
        exit;
    }

    public function inscription(array $dVueErreur)
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';
        $role = $_POST['role'] ?? '';


        Validation::val_user($username, $password, $confirm, $dVueErreur);

        if (!empty($dVueErreur)) {
            $dVueErreur[] = "erreur dans l'inscription";
            $this->afficherVue('erreur', $dVueErreur, $results = null);
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


        $user = new User(null, $username, $hashedPassword,$role);
        $this->userGateway->login($user);


    }

    public function afficheConnection(array $dVueErreur)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sousAction = $_POST['action'] ?? '';

            switch ($sousAction) {
                case 'connection':
                    $this->connection($dVueErreur);
                    break;
            }


            header("Location: /siteSAE2A/home");
            exit;
        }
        $this->afficherVue('connection', $dVueErreur, $results = null);

    }

    public function connection(array &$dVueErreur)
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $savepass = $this->userGateway->getHashPass($username, $password);
        $_SESSION['role'] = $this->userGateway->getRole($username);
        $clientId = $this->userGateway->getClientId($username);

        Validation::val_connection($username, $password, $savepass, $dVueErreur);


        session_regenerate_id(true);
        $_SESSION['username'] = $username;
        $_SESSION['idClient'] = $clientId;
        


        if (!empty($dVueErreur)) {
            $this->afficherVue('erreur', $dVueErreur, $results = null);
            exit;
        }

    }

    public function cars(array $dVueErreur)
    {
        // 1. Récupération des filtres depuis l'URL (GET)
        $date_depart = $_GET['date_depart'] ?? null;
        $date_retour = $_GET['date_retour'] ?? null;

        $boite_filtre = $_GET['boite'] ?? null;
        $energie_filtre = $_GET['energie'] ?? null; 
        $prix_min = isset($_GET['prix_min']) && $_GET['prix_min'] !== '' ? (float)$_GET['prix_min'] : null;
        $prix_max = isset($_GET['prix_max']) && $_GET['prix_max'] !== '' ? (float)$_GET['prix_max'] : null;

        // 2. Appel de l'API
        try {
            // On prépare les query parameters pour l'API
            $queryParams = [
                'query' => [
                    'date_depart' => $date_depart,
                    'date_retour' => $date_retour,
                ]
            ];
            // On passe le tableau de configuration en deuxième argument
            $response = $this->apiClient->get('voituresForReservation', $queryParams);
            $results = json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $dVueErreur[] = "Impossible de récupérer les véhicules depuis l’API.";
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

    $this->afficherVue('cars', $dVueErreur, $results);
    }

    public function reservationForm(array $dVueEreur){
         $this->afficherVue('reservationForm', $dVueEreur, $results = null);
    }

    public function afficheRecapitulatif(array $dVueErreur)
    {
        $this->afficherVue('recapitulatif', $dVueErreur, $results = null);
    }

    public function finaliserReservation(array &$dVueErreur) 
    {
        try {
            $nom = $_POST['nom'] ?? null;
            $prenom = $_POST['prenom'] ?? null;
            $email = $_POST['email'] ?? null;
            $tel = $_POST['numTel'] ?? null;
            $permis = $_POST['numPermis'] ?? null;
            $dateNaiss = $_POST['datenaiss'] ?? null;
            $nationalite = $_POST['nationalite'] ?? null;

            // 2. Récupération des données de base de la réservation
            $numSerie = $_POST['num_serie'] ?? null;
            $dateDebut = $_POST['date_debut'] ?? null;
            $dateFin = $_POST['date_fin'] ?? null;

            // --- NOUVELLES VÉRIFICATIONS ---
            // 1. Vérification de l'âge (18 ans minimum)
            $dateN = new \DateTime($dateNaiss);
            $aujourdhui = new \DateTime();
            $age = $aujourdhui->diff($dateN)->y;
            if ($age < 18) {
                throw new \Exception("Vous devez avoir au moins 18 ans pour réserver.");
            }

            // 2. Vérification des dates de réservation
            $debut = new \DateTime($dateDebut);
            $fin = new \DateTime($dateFin);
            $now = new \DateTime('today'); // Minuit aujourd'hui

            if ($debut < $now) {
                throw new \Exception("La date de départ ne peut pas être dans le passé.");
            }
            if ($fin < $debut) {
                throw new \Exception("La date de retour doit être égale ou supérieure à la date de départ.");
            }

            // On SELECT le véhicule par son NumSerie pour garantir l'exactitude des données
            $responseVehicule = $this->apiClient->get("voitures/$numSerie"); 
            $vehicule = json_decode($responseVehicule->getBody()->getContents(), true);

            if (!$vehicule) {
                throw new \Exception("Véhicule introuvable pour le contrat.");
            }
            
            // --- ÉTAPE 1 : Créer le client via l'API ---
            try {
                $responseClient = $this->apiClient->post('client', [
                    'json' => [
                        'Nom' => $nom,
                        'Prenom' => $prenom,
                        'Email' => $email,
                        'NumTel' => $tel,
                        'NumPermis' => $permis,
                        'DateNaiss' => $dateNaiss,
                        'Nationalite' => $nationalite
                    ]
                ]);
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    $body = $e->getResponse()->getBody()->getContents();
                    $data = json_decode($body, true);

                    if ($e->getResponse()->getStatusCode() === 409) {
                        $dVueErreur[] = "Conflit d'identité : les informations saisies (Email ou Permis) sont déjà liées à un autre compte.";
                        $dVueErreur[] = $data['message'] ?? "Erreur de doublon en base de données.";
                    } else {
                        $dVueErreur[] = "L'API a rencontré un problème lors du traitement.";
                        $dVueErreur[] = $e->getMessage();
                    }
                } else {
                    $dVueErreur[] = "Impossible de contacter le serveur distant.";
                }
                $this->afficherVue('erreur', $dVueErreur, null);
                return;
            }

            $dataClient = json_decode($responseClient->getBody()->getContents(), true);
            $idClient = $dataClient['idClient'] ?? null;

            if ($idClient) {
                // --- ÉTAPE 2 : Créer le contrat via l'API ---
                $responseContrat = $this->apiClient->post('modif/contrat', [
                    'json' => [
                        'DateDebut'  => $dateDebut,
                        'DateFin'    => $dateFin,
                        'Statut'     => 'EnCoursValidation',
                        'IdClient'   => (int)$idClient,
                        'EtatAvant'  => 5,
                        'IdVehicule' => $numSerie,
                        'Marque'     => $vehicule['Marque'], 
                        'NomModele'  => $vehicule['Nom'],    
                        'AnneeModele'=> $vehicule['Annee']   
                    ]
                ]);

                $dataContrat = json_decode($responseContrat->getBody()->getContents(), true);

                if (isset($dataContrat['message']) && $dataContrat['message'] === 'Contrat créé avec succès') {
                    $this->afficherVue('confirmationSucces', $dVueErreur);
                } else {
                    $dVueErreur[] = "Erreur lors de la création du contrat";
                    $this->afficherVue('erreur', $dVueErreur, null);
                }
            } else {
                $dVueErreur[] = "Erreur lors de la création du client";
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

    public function listeReservation(array $dVueErreur = [])
    {
        /*  ---->>> ANCIEN CODE DE CLEMENT AVEC GATEWAY A CHANGER AVEC FONCTION : rechercherReservation() PRESENTE EN DESSOUS, 
            ---->>> ET NON PAS LA GATEWAY, EXEMPLE DANS LE employeController.php SI JAMAIS


        $reservationGateway = new \modele\ReservationGateway($this->connection);
        
        $idClient = $_SESSION['idClient'] ?? 0; 
        
        // Nettoyage des variables GET
        $filtre = $_GET['filtre'] ?? 'toutes';
        $champ = $_GET['champ'] ?? 'idContrat';
        $q = trim($_GET['q'] ?? ''); // <-- Le trim() ici est très important !
        

        $results = $reservationGateway->searchReservations('IdClient', (string)$idClient, $filtre);
        if ($this->getRole() == 'admin' || $this->getRole() == 'employe') {
            $results = $reservationGateway->getAllReservations();
        } else {
            
        }
        */
        $results=[];
        $dVueErreur=['PAGE non faite pour user'];
        $this->afficherVue('erreur', $dVueErreur, $results);
    }

    private function rechercherReservation(array &$dVueEreur = []): void
    {
        $champ = $_GET['champ'] ?? 'idContrat';
        $q = trim($_GET['q'] ?? '');
        $filtre = $_GET['filtre'] ?? 'en-cours';

        $results = [];
        try {
            $response = $this->apiClient->get('contrat');
            $allContrats = json_decode($response->getBody()->getContents(), true) ?? [];

            $today = date('Y-m-d');

            $filtered = array_filter($allContrats, function ($c) use ($champ, $q, $filtre, $today) {

                $match = false;
                switch ($filtre) {
                    case 'a-valider':
                        $match = (isset($c['Statut']) && $c['Statut'] === 'EnCoursValidation');
                        break;
                    case 'a-venir':
                        $match = ($c['DateDebut'] > $today);
                        break;
                    case 'passees':
                        $match = ($c['DateFin'] < $today);
                        break;
                    case 'toutes':
                        $match = true;
                        break;
                    case 'en-cours':
                    default:
                        $match = ($c['DateDebut'] <= $today && $c['DateFin'] >= $today);
                        break;
                }
                if (!$match)
                    return false;

                if ($q !== '') {
                    $idContrat = $c['idContrat'] ?? $c['IdContrat'] ?? '';
                    $idClient = $c['IdClient'] ?? $c['idClient'] ?? '';
                    $idVehicule = $c['IdVehicule'] ?? $c['idVehicule'] ?? '';

                    if ($champ === 'idContrat' && (string) $idContrat !== $q)
                        return false;
                    if ($champ === 'Client' && (string) $idClient !== $q)
                        return false;
                    if ($champ === 'Vehicule' && stripos((string) $idVehicule, $q) === false)
                        return false;
                }
                return true;
            });

            foreach ($filtered as $c) {
                $results[] = [
                    'idContrat' => $c['idContrat'] ?? $c['IdContrat'] ?? null,
                    'IdClient' => $c['IdClient'] ?? $c['idClient'] ?? null,
                    'idVehicule' => $c['IdVehicule'] ?? $c['idVehicule'] ?? null,
                    'DateDebut' => $c['DateDebut'] ?? null,
                    'DateFin' => $c['DateFin'] ?? null,
                    'Statut' => $c['Statut'] ?? null
                ];
            }

        } catch (RequestException $e) {
            $dVueEreur[] = "Impossible de récupérer les contrats via l'API.";
        }

        $this->afficherVue('reservation', $dVueEreur, $results);
    }
    public function listeVoitures(array $dVueEreur)
    {
        $sousAction = $_GET['action'] ?? '';
        if ($sousAction === 'rechercherVoitures') {
            $this->rechercherVoitures($dVueEreur);
            return;
        }
        try {
            $response = $this->apiClient->get('voitures');

            $results = json_decode(
                $response->getBody()->getContents(),
                true
            );

        } catch (RequestException $e) {
            $dVueEreur[] = "Impossible de récupérer les véhicules depuis l’API.";
            $results = [];
        }

        $this->afficherVue('flotte', $dVueEreur, $results);

    }

    private function rechercherVoitures(array $dVueErreur = []): void
    {
        $motCle = trim($_GET['q'] ?? '');

        try {
            if ($motCle === '') {
                $response = $this->apiClient->get("voitures");
            } else {
                $response = $this->apiClient->get("voitures", [
                    'query' => ['nom' => $motCle]
                ]);
            }
            $results = json_decode($response->getBody()->getContents(), true);

            if (empty($results)) {
                $dVueErreur[] = "Aucune voiture trouvée pour \"$motCle\".";
            }
        } catch (\Exception $e) {
            $dVueErreur[] = "Erreur lors de la recherche via l’API : " . $e->getMessage();
            $results = [];
        }

        $this->afficherVue('flotte', $dVueErreur, $results);
    }


    public function afficheParametres(array $dVueErreur)
    {

        $this->afficherVue('parametres', $dVueErreur, $results = null);
    }
}