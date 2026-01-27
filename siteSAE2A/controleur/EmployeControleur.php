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
                case "listeUtilisateur":
                            $this->listeUtilisateur($dVueErreur);
                            break;
                case 'listeReservation':
                            $this->listeReservation($dVueErreur);
                            break;
                case 'homeCustomers':
                    $this->homeCustomers($dVueErreur);
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


private function afficherVue(string $vueKey, array $dVueEreur, ?array $results = null, string $role="employe")
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

}
?>
