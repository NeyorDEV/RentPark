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
class AdminControleur
{
    private Connection $connection;
    private VehicleGateway $gateway;
    private UserGateway $userGateway;
    private ReservationGateway $reservationGateway;

    // test pour l'API
    private Client $apiClient;

    public function __construct()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            header('Location: /siteSAE2A/connection');
            exit;
        }
        
        global $rep, $vues, $user, $pass, $dsn, $action;

        $dVueEreur = [];

        try {

            $this->connection = new Connection($dsn, $user, $pass);

            $this->gateway = new VehicleGateway($this->connection);
            $this->userGateway = new UserGateway($this->connection);
            $this->reservationGateway = new ReservationGateway($this->connection);

            // test pour l'API
            $this->apiClient = new Client([
                'base_uri' => 'http://localhost:8880/',
                'timeout' => 2.0
            ]);


            switch ($action) {
                case "listeVoitures":
                    $this->listeVoitures($dVueEreur);
                    break;
                case "afficheInscription":
                    $this->afficheInscription($dVueEreur);
                    break;
                case "afficheDashboard":
                    $this->afficheDashboard($dVueEreur);
                    break;
                case "affichePlanning":
                        $this->affichePlanning($dVueEreur);
                        break;
                case "afficheConnection":
                    $this->afficheConnection($dVueEreur);
                    break;
                case "rechercherVoitures":
                    $this->rechercherVoitures($dVueEreur);
                    break;

                case "listeUtilisateur":
                    $this->listeUtilisateur($dVueEreur);
                    break;
                case 'listeReservation':
                    $this->listeReservation($dVueEreur);
                    break;
                case 'deconnecter':
                    $this->deconnecter();
                    break;
                case 'homeCustomers':
                    $this->homeCustomers($dVueEreur);
                    break;
                case 'cars':
                    $this->cars($dVueEreur);
                    break;
                case 'reservationForm':
                    $this->reservationForm($dVueEreur);
                    break;
                case 'afficheRecapitulatif':
                    $this->afficheRecapitulatif($dVueEreur);
                    break;
                case 'afficheParametres':
                    $this->afficheParametres($dVueEreur);
                    break;
                case 'finaliserReservation':
                    $this->finaliserReservation($dVueEreur);
                    break;
                default:
                    $dVueEreur[] = "Action inconnue";
                    $this->afficherVue('homeCustomers', $dVueEreur, $results = null, 'admin');
                    break;
            }

        } catch (\PDOException $e) {
            $dVueEreur[] = "Erreur BDD : " . $e->getMessage();
            $this->afficherVue('erreur', $dVueEreur, $results = null, 'admin');
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

    // ajouter les vue erreur et les vérif 

    public function afficheDashboard(array $dVueEreur)
    {
        $results = [
            "revenusMensuels"   => $this->reservationGateway->getMonthlyIncome(),
            "voiturePlusLouee"  => $this->gateway->getMostRentedCar(),
            "totalUsers"      => $this->userGateway->countUser()
        ];

        $alerts = [];

    // Contrôle technique < 2 mois
    $vehiculesCT = $this->gateway->getVehiculesControleTechniqueBientotExpire();

    foreach ($vehiculesCT as $v) {
        $alerts[] = [
            "label"    => "Contrôle technique",
            "vehicule" => $v["Marque"] . " " . $v["Modele"]
        ];
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
    usort($planning, fn($a,$b) => strcmp($a['time'], $b['time']));

    $results['planning'] = $planning;




    // Tri chronologique
    usort($planning, fn($a,$b) => strcmp($a['time'], $b['time']));       

        $this->afficherVue('dashboard', $dVueEreur, $results, 'admin');
    }

    public function homeCustomers(array $dVueEreur)
    {

        $this->afficherVue('homeCustomers', $dVueEreur, $results = null, 'admin');

    }

    // --- Dans AdminControleur.php, méthode cars() ---

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
        $response = $this->apiClient->get('vehicules');
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

    public function reservationForm(array $dVueEreur){
         $this->afficherVue('reservationForm', $dVueEreur, $results = null, 'admin');
    }

    public function afficheRecapitulatif(array $dVueEreur)
    {
        $this->afficherVue('recapitulatif', $dVueEreur, $results = null, 'admin');
    }

    public function afficheParametres(array $dVueEreur)
    {
        
        $this->afficherVue('parametres', $dVueEreur, $results = null, 'admin');
    }

    public function affichePlanning(array $dVueEreur)
    {
        // 🔹 1. Récupération du mois depuis l’URL
        $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
        $year  = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');
    
        $month = max(1, min(12, $month)); // sécurité
    
        // 🔹 2. Calcul mois précédent / suivant
        $prevMonth = $month - 1;
        $prevYear  = $year;
        $nextMonth = $month + 1;
        $nextYear  = $year;
    
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
                'label'   => $day,
                'isToday' => ($date === $today),
                'events'  => []
            ];
        }
    
        // 🔹 6. Ajout des événements
        foreach ($contracts as $c) {
    
            if (isset($calendar[$c['DateDebut']])) {
                $calendar[$c['DateDebut']]['events'][] = [
                    'type'  => 'depart',
                    'label' => 'Départ ' . $c['Marque'] . ' ' . $c['Nom']
                ];
            }
    
            if (isset($calendar[$c['DateFin']])) {
                $calendar[$c['DateFin']]['events'][] = [
                    'type'  => 'retour',
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
    

    private function ajouterVoiture(array $dVueEreur)
{
    try {
        // ===============================
        // 1. Upload image (comme avant)
        // ===============================
        $imagePath = '';

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $nomTemp = $_FILES['image']['tmp_name'];
            $nomFichier = uniqid() . '_' . basename($_FILES['image']['name']);
            $dossier = __DIR__ . '/../html/icons/' . $nomFichier;

            if (move_uploaded_file($nomTemp, $dossier)) {
                $imagePath = 'html/icons/' . $nomFichier;
            }
        }

        // ===============================
        // 2. Construction payload API
        // ===============================
        $payload = [
            'NumSerie' => $_POST['NumSerie'] ?? null,
            'Energie' => $_POST['Energie'] ?? null,
            'NbPlaces' => $_POST['NbPlaces'] ?? null,
            'Categorie' => $_POST['Categorie'] ?? null,
            'Transmission' => $_POST['Transmission'] ?? 'Traction',
            'Boite' => $_POST['Boite'] ?? 'Manuelle',
            'Etat' => $_POST['Etat'] ?? 'Libre',
            'Puissance' => $_POST['Puissance'] ?? null,
            'DateAchat' => $_POST['DateAchat'] ?? null,
            'DateExpirationControleTech' => $_POST['DateExpirationControleTech'] ?? null,
            'DateDernierControleTech' => $_POST['DateDernierControleTech'] ?? null,
            'Marque' => $_POST['Marque'] ?? null,
            'Nom' => $_POST['Nom'] ?? null,
            'Annee' => $_POST['Annee'] ?? null,
            'IdAssureur' => (int) ($_POST['IdAssureur'] ?? 0),
            'IdFournisseur' => (int) ($_POST['IdFournisseur'] ?? 0),
            'ImagePath' => $imagePath,
            'Couleur' => $_POST['Couleur'] ?? null,
            'Prix' => $_POST['Prix'] ?? null
        ];

        // ===============================
        // 3. Appel API POST /vehicule
        // ===============================
        $response = $this->apiClient->post('vehicule', [
            'json' => $payload
        ]);

        // ===============================
        // 4. Succès → redirection
        // ===============================
        if ($response->getStatusCode() === 201) {
            header("Location: /siteSAE2A/voitures");
            exit;
        }

    } catch (RequestException $e) {

        // ===============================
        // 5. Gestion erreurs API
        // ===============================
        if ($e->hasResponse()) {
            $apiError = json_decode(
                $e->getResponse()->getBody()->getContents(),
                true
            );

            $dVueEreur[] = $apiError['error'] ?? 'Erreur API inconnue.';
        } else {
            $dVueEreur[] = "Impossible de contacter l’API.";
        }
    }

    // ===============================
    // 6. Retour vue avec erreurs
    // ===============================
    $this->afficherVue('flotte', $dVueEreur, [], 'admin');
}


    private function supprimerVoiture(array $dVueEreur)
    {
        $id = ($_POST['NumSerie'] ?? -1);
        try {
            $this->apiClient->delete("/delete/voitures/$id");
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

    private function afficherVue(string $vueKey, array $dVueEreur, ?array $results = null, string $role="user")
    {
        global $rep, $vues, $twig;

        $role = $_SESSION['role'] ?? 'visiteur';



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
        $this->afficherVue('user', $dVueEreur, $results, 'admin');
    }


    // ---------------------------| Reservations |-----------------------------------------------------------
    private function rechercherReservation(): void
    {
        $champ = $_GET['champ'] ?? 'idContrat';
        $q = trim($_GET['q'] ?? '');
        $filtre = $_GET['filtre'] ?? 'en-cours';

        // whitelist des champs autorisé
        $allowed = ['idContrat' => 'idContrat', 'Vehicule' => 'Vehicule', 'Client' => 'Client'];
        if (!isset($allowed[$champ])) {
            $champ = 'idContrat';
        }

        $results = $this->reservationGateway->searchReservations($champ, $q, $filtre);

        $this->afficherVue('reservation', [], $results, 'admin');
    }
    private function ajouterReservation(array $post): void
    {
        $vehicule = trim($post['Vehicule'] ?? '');
        $client = (int) ($post['Client'] ?? 0);
        $dateDebut = trim($post['DateDebut'] ?? '');
        $dateFin = trim($post['DateFin'] ?? '');

        $err = [];

        if ($vehicule === '')
            $err[] = "Le véhicule (VIN) est obligatoire.";
        if ($client <= 0)
            $err[] = "Le client (ID) doit être un entier positif.";
        if ($dateDebut === '')
            $err[] = "La date de début est obligatoire.";
        if ($dateFin === '')
            $err[] = "La date de fin est obligatoire.";

        $d1 = \DateTime::createFromFormat('Y-m-d', $dateDebut) ?: null;
        $d2 = \DateTime::createFromFormat('Y-m-d', $dateFin) ?: null;
        if (!$d1 || !$d2) {
            $err[] = "Format de date invalide (attendu : AAAA-MM-JJ).";
        } elseif ($d1 > $d2) {
            $err[] = "La date de début doit être antérieure ou égale à la date de fin.";
        }
        if (empty($err)) {
            try {
                $this->reservationGateway->insertReservation($vehicule, $client, $dateDebut, $dateFin);
            } catch (\PDOException $e) {
                $err[] = "Erreur base de données : " . $e->getMessage();
            }
        }
        $this->rechercherReservation();
    }
    private function modifierReservation(array $post): void
    {
        $id = (int) ($post['id'] ?? 0);
        $vehicule = trim($post['Vehicule'] ?? '');
        $client = (int) ($post['Client'] ?? 0);
        $dateDebut = trim($post['DateDebut'] ?? '');
        $dateFin = trim($post['DateFin'] ?? '');

        $err = [];

        if ($id <= 0)
            $err[] = "Identifiant de contrat invalide.";
        if ($vehicule === '')
            $err[] = "Le véhicule (VIN) est obligatoire.";
        if ($client <= 0)
            $err[] = "Le client (ID) doit être un entier positif.";
        if ($dateDebut === '')
            $err[] = "La date de début est obligatoire.";
        if ($dateFin === '')
            $err[] = "La date de fin est obligatoire.";

        $d1 = \DateTime::createFromFormat('Y-m-d', $dateDebut) ?: null;
        $d2 = \DateTime::createFromFormat('Y-m-d', $dateFin) ?: null;
        if (!$d1 || !$d2) {
            $err[] = "Format de date invalide (attendu : AAAA-MM-JJ).";
        } elseif ($d1 > $d2) {
            $err[] = "La date de début doit être antérieure ou égale à la date de fin.";
        }
        if (empty($err)) {
            try {
                $this->reservationGateway->update($id, $vehicule, $client, $dateDebut, $dateFin);
                header('Location: /siteSAE2A/reservation');
                exit;
            } catch (\PDOException $e) {
                $err[] = "Erreur base de données : " . $e->getMessage();
            }
        }
        $this->rechercherReservation();
    }
    private function supprimerReservation(array $dVueEreur)
    {
        $id = (int) ($_POST['id'] ?? -1);
        if ($id >= 0) {
            $this->reservationGateway->delete($id);
        } else {
            $dVueErreur[] = "Cette réservation n'existe pas, impossible de la supprimer .";
        }

        $this->rechercherReservation();
        exit;
    }
    public function listeReservation(array $dVueEreur)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sousAction = $_POST['action'] ?? '';

            switch ($sousAction) {
                case 'ajouterReservation':
                    $this->ajouterReservation($_POST);
                    break;
                case 'modifierReservation':
                    $this->modifierReservation($_POST);
                    break;
                case 'supprimerReservation':
                    $this->supprimerReservation($dVueEreur);
                    break;
            }
            exit;
        }
        $sousAction = $_GET['action'] ?? '';
        if ($sousAction === 'rechercherReservation') {
            $this->rechercherReservation();
            return;
        }

        $results = $this->reservationGateway->searchReservations('idContrat', '', 'en-cours');
        $this->afficherVue('reservation', [], $results, 'admin');
    }
    // ------------------------------------------------------------------------------------------------------
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

    public function deconnecter(): void
    {
        session_unset();
        session_destroy();
        header("Location: /siteSAE2A/connection");
        exit;
    }

    function finaliserReservation(array &$dVueEreur) 
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
                    // CECI VA AFFICHER L'ERREUR PHP RÉELLE DE L'API
                    echo $e->getResponse()->getBody()->getContents(); 
                    die(); 
            }
}

            $dataClient = json_decode($responseClient->getBody()->getContents(), true);
            $idClient = $dataClient['idClient'] ?? null;

            if ($idClient) {
                // --- ÉTAPE 2 : Créer le contrat via l'API ---
                $responseContrat = $this->apiClient->post('contrat', [
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
                    $this->afficherVue('confirmationSucces', $dVueEreur, null, 'admin');
                }
            }

        } catch (RequestException $e) {
            $dVueEreur[] = "Erreur API : " . $e->getMessage();
            $this->afficherVue('recapitulatif', $dVueEreur, null, 'admin');
        } catch (\Exception $e) {
            $dVueEreur[] = "Erreur technique : " . $e->getMessage();
            $this->afficherVue('recapitulatif', $dVueEreur, null, 'admin');
        }
    }
}
?>