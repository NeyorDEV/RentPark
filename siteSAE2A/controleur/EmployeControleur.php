<?php
namespace controleur;
use modele\Connection;
use modele\VehicleGateway;
use modele\ReservationGateway;
use modele\UserGateway;
use GuzzleHttp\Client;
use config\Validation;
use GuzzleHttp\Exception\RequestException;

class EmployeControleur
{
    private Connection $connection;
    private VehicleGateway $gateway;

    private UserGateway $userGateway;

    private ReservationGateway $reservationGateway;

    private Client $apiClient;

    public function __construct()
    {
        global $rep, $vues, $user, $pass, $dsn, $action;

        $dVueErreur = [];

        try {
            $this->connection = new Connection($dsn, $user, $pass);
            $this->gateway = new VehicleGateway($this->connection);
            $this->userGateway = new UserGateway($this->connection);
            $this->reservationGateway = new ReservationGateway($this->connection);

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
                case "affichePlanning":
                    $this->affichePlanning($dVueErreur);
                    break;
                case "listeVoitures":
                    $this->listeVoitures($dVueErreur);
                    break;
                case 'deconnecter':
                    $this->deconnecter();
                    break;
                case 'afficheParametres':
                    $this->afficheParametres($dVueErreur);
                    break;
                case 'cars':
                        $this->cars($dVueErreur);
                        break;
                case "listeClients":
                            $this->listeClients($dVueErreur);
                            break;
                case "listeUtilisateurs":
                            $this->listeUtilisateurs($dVueErreur);
                            break;
                case 'listeReservation':
                    $this->listeReservation($dVueErreur);
                    break;
                case 'homeCustomers':
                    $this->homeCustomers($dVueErreur);
                    break;
                case 'finaliserReservation':
                    $this->finaliserReservation($dVueErreur);
                    break;
                case 'afficheRecapitulatif':
                    $this->afficheRecapitulatif($dVueErreur);
                    break;
                default:
                    $dVueEreur[] = "Action inconnue";
                    $this->afficherVue('homeCustomers', $dVueEreur, $results = null, 'admin');
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
        $this->afficherVue('inscription', $dVueEreur, $results = null, 'admin');

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
            $this->afficherVue('erreur', $dVueErreur, $results = null, 'admin');
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
        $this->afficherVue('connection', $dVueEreur, $results = null, 'admin');

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
            $this->afficherVue('erreur', $dVueErreur, $results = null, 'admin');
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

        $prix_min = isset($_GET['prix_min']) && $_GET['prix_min'] !== '' ? (float) $_GET['prix_min'] : null;
        $prix_max = isset($_GET['prix_max']) && $_GET['prix_max'] !== '' ? (float) $_GET['prix_max'] : null;

        // 2. Appel de l'API
        try {
            $response = $this->apiClient->get('voituresForReservation');
            $results = json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $dVueEreur[] = "Impossible de récupérer les véhicules depuis l’API.";
            $results = [];
        }

        // 3. Application des filtres PHP
        if (!empty($results)) {
            $results = array_filter($results, function ($voiture) use ($boite_filtre, $energie_filtre, $prix_min, $prix_max) {
                $match = true;
                $prixVoiture = isset($voiture['Prix']) ? (float) $voiture['Prix'] : 0;

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


    private function afficherVue(string $vueKey, array $dVueEreur, ?array $results = null, string $role = "employe")
    {
        global $rep, $vues, $twig;

        $role = $_SESSION['role'] ?? 'user';



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
                'role' => $role
            ]);
            return;
        }

        // php
        $cheminVue = realpath($rep . $vuePath);
        if ($cheminVue && file_exists($cheminVue)) {
            $resultsTwig = $results;
            require_once($cheminVue); // NOSONAR
        } else {
            echo "Fichier de vue introuvable : " . ($rep . $vuePath);
            exit;
        }
    }

    private function ajouterVoiture(array $dVueEreur)
    {

        $imagePath = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $nomTemp = $_FILES['image']['tmp_name'];
            $nomFichier = uniqid() . '_' . basename($_FILES['image']['name']);
            $dossier = __DIR__ . '/../html/icons/' . $nomFichier;

            if (move_uploaded_file($nomTemp, $dossier)) {
                $imagePath = 'html/icons/' . $nomFichier;
            }
        }

        $modele = $_POST['modele'] ?? '';
        $couleur = $_POST['couleur'] ?? '';
        $puissance = $_POST['puissance'] ?? '';


        Validation::val_voiture($modele, $couleur, $puissance, $dVueEreur);

        if (empty($dVueEreur)) {
            $this->gateway->add($modele, $couleur, $puissance, $imagePath);
            header("Location: /sitesae2A/voitures");
            exit;
        }

        $results = $this->gateway->getAll();
        $this->afficherVue('flotte', $dVueEreur, $results, 'admin');
    }

    private function supprimerVoiture(array $dVueEreur)
    {
        $id = ($_POST['NumSerie'] ?? -1);
        try {
            $this->apiClient->delete("/voitures/$id");
        } catch (RequestException $e) {
            $dVueEreur[] = "Erreur lors de la suppression via l’API.";
        }
        header("Location: /sitesae2A/voitures");
        exit;
    }

    private function modifierVoiture(array $dVueEreur)
    {
        $id = (int) ($_POST['id'] ?? 0);
        $modele = $_POST['modele'] ?? '';
        $couleur = $_POST['couleur'] ?? '';
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

    private function rechercherClient(array $dVueErreur = []): void
    {
        //A faire
        $motCle = trim($_GET['q'] ?? '');

        if ($motCle === '') {
            //A faire
        } else {
            // A faire

            if (empty($results)) {
                $dVueErreur[] = "Aucun Client trouvée pour \"$motCle\".";
            }
        }

        $this->afficherVue('client', $dVueErreur, $results, 'admin');
    }


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


    private function ajouterUtilisateur(array $dVueEreur)
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';
        $role = $_POST['role'] ?? '';

        Validation::val_user($username, $password, $confirm, $role, $dVueEreur);

        if (empty($dVueEreur)) {
            $this->userGateway->addUser($username, $password, $role);
            header("Location: /siteSAE2A/utilisateurs");
            exit;

        }

        $response = $this->apiClient->get('users');
        $results = json_decode($response->getBody()->getContents(), true);
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

    private function modifierUtilisateur(array $dVueEreur)
    {
        $username = $_POST['username'] ?? '';
        $id = (int) ($_POST['id'] ?? -1);



        if (empty($dVueEreur)) {

            $this->userGateway->update($username, $id);
            header("Location: /sitesae2A/utilisateurs");
            exit;
        }
        $results = $this->gateway->getAll();
        $this->afficherVue('user', $dVueEreur, $results, 'admin');
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
        $this->afficherVue('client', $dVueEreur, $results, 'admin');
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
        $this->afficherVue('user', $dVueEreur, $results, 'admin');
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
        $this->afficherVue('client', $dVueEreur, $results, 'admin');
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

        $this->afficherVue('flotte', $dVueEreur, $results, 'admin');

    }

    // ---------------------------| Reservations |-----------------------------------------------------------
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

        $this->afficherVue('reservation', $dVueEreur, $results, 'admin');
    }

    private function ajouterReservation(array $post, array &$dVueErreur): void
    {
        $vehicule = trim($post['Vehicule'] ?? '');
        $client = (int) ($post['Client'] ?? 0);
        $dateDebut = trim($post['DateDebut'] ?? '');
        $dateFin = trim($post['DateFin'] ?? '');

        try {
            $this->apiClient->post('modif/contrat', [
                'json' => [
                    'DateDebut' => $dateDebut,
                    'DateFin' => $dateFin,
                    'IdVehicule' => $vehicule,
                    'IdClient' => $client,
                    'Statut' => 'EnCoursValidation'
                ]
            ]);
        } catch (RequestException $e) {
            $dVueErreur[] = "Erreur lors de l'ajout via l'API.";
        }

        $this->rechercherReservation($dVueErreur);
    }

    private function modifierReservation(array $post, array &$dVueErreur): void
    {
        $id = (int) ($post['id'] ?? 0);

        try {
            $this->apiClient->put("contrat/{$id}", [
                'json' => [
                    'DateDebut' => trim($post['DateDebut'] ?? ''),
                    'DateFin' => trim($post['DateFin'] ?? ''),
                    'Vehicule' => trim($post['Vehicule'] ?? ''),
                    'Client' => (int) ($post['Client'] ?? 0)
                ]
            ]);
            header('Location: /siteSAE2A/reservation');
            exit;
        } catch (RequestException $e) {
            $dVueErreur[] = "Erreur lors de la modification via l'API.";
        }

        $this->rechercherReservation($dVueErreur);
    }

    private function supprimerReservation(array &$dVueErreur): void
    {
        $id = (int) ($_POST['id'] ?? -1);

        if ($id > 0) {
            try {
                $this->apiClient->delete("contrat/{$id}");
            } catch (RequestException $e) {
                $dVueErreur[] = "Erreur lors de la suppression via l'API.";
            }
        } else {
            $dVueErreur[] = "ID invalide pour la suppression.";
        }

        $this->rechercherReservation($dVueErreur);
    }

    public function listeReservation(array &$dVueErreur)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sousAction = $_POST['action'] ?? '';

            switch ($sousAction) {
                case 'ajouterReservation':
                    $this->ajouterReservation($_POST, $dVueErreur);
                    break;
                case 'modifierReservation':
                    $this->modifierReservation($_POST, $dVueErreur);
                    break;
                case 'supprimerReservation':
                    $this->supprimerReservation($dVueErreur);
                    break;
                case 'changerStatut':
                    $this->changerStatutReservation($_POST, $dVueErreur);
                    break;
            }
            exit;
        }

        $sousAction = $_GET['action'] ?? '';
        if ($sousAction === 'rechercherReservation') {
            $this->rechercherReservation($dVueErreur);
            return;
        }

        $this->rechercherReservation($dVueErreur);
    }

    private function changerStatutReservation(array $post, array &$dVueErreur): void
    {
        $id = (int) ($post['id'] ?? 0);
        $nouveauStatut = trim($post['nouveauStatut'] ?? '');

        if ($id > 0 && in_array($nouveauStatut, ['Validé', 'Annulé'])) {
            try {
                $this->apiClient->patch("contrat/{$id}/statut", [
                    'json' => [
                        'Statut' => $nouveauStatut
                    ]
                ]);
            } catch (RequestException $e) {
                $dVueErreur[] = "Erreur lors du changement de statut via l'API.";
            }
        } else {
            $dVueErreur[] = "Données invalides pour le changement de statut.";
        }

        $this->rechercherReservation($dVueErreur);
    }

    public function afficheParametres(array $dVueEreur)
    {

        $this->afficherVue('parametres', $dVueEreur, $results = null, 'employe');
    }

    public function deconnecter(): void
    {
        session_unset();
        session_destroy();
        header("Location: /siteSAE2A/connection");
        exit;
    }

    public function affichePlanning(array $dVueEreur)
    {
        // 🔹 1. Récupération du mois depuis l’URL
        $month = isset($_GET['month']) ? (int) $_GET['month'] : (int) date('m');
        $year = isset($_GET['year']) ? (int) $_GET['year'] : (int) date('Y');

        $month = max(1, min(12, $month)); // sécurité

        // 🔹 2. Calcul mois précédent / suivant
        $prevMonth = $month - 1;
        $prevYear = $year;
        $nextMonth = $month + 1;
        $nextYear = $year;

        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }

        // 🔹 3. Libellé du mois
        $formatter = new \IntlDateFormatter(
            'fr_FR',
            \IntlDateFormatter::NONE,
            \IntlDateFormatter::NONE
        );

        $formatter->setPattern('MMMM yyyy');

        $dateObj = new \DateTime("$year-$month-01");
        $currentMonthLabel = ucfirst($formatter->format($dateObj));



        // 🔹 4. Récupération des contrats (inchangé)
        $contracts = $this->reservationGateway->getMonthlyPlanning();

        // 🔹 5. Génération du calendrier du mois demandé
        $calendar = [];
        $today = date('Y-m-d');

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);

            $calendar[$date] = [
                'label' => $day,
                'isToday' => ($date === $today),
                'events' => []
            ];
        }

        // 🔹 6. Ajout des événements
        foreach ($contracts as $c) {

            if (isset($calendar[$c['DateDebut']])) {
                $calendar[$c['DateDebut']]['events'][] = [
                    'type' => 'depart',
                    'label' => 'Départ ' . $c['Marque'] . ' ' . $c['Nom']
                ];
            }

            if (isset($calendar[$c['DateFin']])) {
                $calendar[$c['DateFin']]['events'][] = [
                    'type' => 'retour',
                    'label' => 'Retour ' . $c['Marque'] . ' ' . $c['Nom']
                ];
            }
        }

        // 🔹 7. Envoi à la vue
        $results = [
            'calendar' => $calendar,
            'prevMonth' => $prevMonth,
            'prevYear' => $prevYear,
            'nextMonth' => $nextMonth,
            'nextYear' => $nextYear,
            'currentMonthLabel' => $currentMonthLabel
        ];

        $this->afficherVue('planning', $dVueEreur, $results, 'admin');
    }


    public function finaliserReservation(array &$dVueEreur)
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

            try {
                // --- ÉTAPE 1 : Créer le client via l'API ---
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
                    $dVueEreur[] = $e->getResponse()->getBody()->getContents();
                    $this->afficherVue('reservationForm', $dVueEreur, null, 'user');
                    return;
                }
            }

            $dataClient = json_decode($responseClient->getBody()->getContents(), true);
            $idClient = $dataClient['idClient'] ?? null;

            if ($idClient) {
                // --- ÉTAPE 2 : Créer le contrat via l'API ---
                $responseContrat = $this->apiClient->post('modif/contrat', [
                    'json' => [
                        'DateDebut' => $dateDebut,
                        'DateFin' => $dateFin,
                        'Statut' => 'EnCoursValidation',
                        'IdClient' => (int) $idClient,
                        'EtatAvant' => 5,
                        'IdVehicule' => $numSerie,
                        'Marque' => $vehicule['Marque'],
                        'NomModele' => $vehicule['Nom'],
                        'AnneeModele' => $vehicule['Annee']
                    ]
                ]);

                $dataContrat = json_decode($responseContrat->getBody()->getContents(), true);

                if (isset($dataContrat['message']) && $dataContrat['message'] === 'Contrat créé avec succès') {
                    $this->afficherVue('confirmationSucces', $dVueEreur, null, 'user');
                } else {
                    $dVueEreur[] = "Erreur lors de la création du contrat";
                    $this->afficherVue('confirmationSucces', $dVueEreur, null, 'user');
                }
            } else {
                $dVueEreur[] = "Erreur lors de la création du client";
                $this->afficherVue('confirmationSucces', $dVueEreur, null, 'user');
            }


        } catch (RequestException $e) {
            $dVueEreur[] = "Erreur API : " . $e->getMessage();
            $this->afficherVue('reservationForm', $dVueEreur, null, 'user');
        } catch (\Exception $e) {
            $dVueEreur[] = "Erreur technique : " . $e->getMessage();
            $this->afficherVue('reservationForm', $dVueEreur, null, 'user');
        }
    }

    public function afficheRecapitulatif(array $dVueEreur)
    {
        $this->afficherVue('recapitulatif', $dVueEreur, $results = null, 'user');
    }

}
?>