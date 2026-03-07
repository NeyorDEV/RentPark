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
    use RoleAwareTrait;
    private Connection $connection;
    private VehicleGateway $gateway;

    private UserGateway $userGateway;

    private ReservationGateway $reservationGateway;

    private Client $apiClient;

    public function __construct()
    {
        global $rep, $vues, $user, $pass, $dsn, $action;

        $this->checkEmploye();

        
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
                case "affichePlanning":
                    $this->affichePlanning($dVueErreur);
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
                case 'listeVoitures':
                    $this->listeVoitures($dVueErreur);
                    break;
                default:
                    $dVueEreur[] = "Action inconnue";
                    $this->afficherVue('homeCustomers', $dVueEreur, $results = null);
                    break;
            }

        } catch (\PDOException $e) {
            $dVueErreur[] = "Erreur BDD : " . $e->getMessage();
            $this->afficherVue('erreur', $dVueErreur);
        }

        exit(0);
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
        $this->afficherVue('flotte', $dVueEreur, $results);
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
        $this->afficherVue('flotte', $dVueEreur, $results);
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

        $this->afficherVue('flotte', $dVueErreur, $results);
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

        if ($motCle === '') {
            //A faire
        } else {
            // A faire

            if (empty($results)) {
                $dVueErreur[] = "Aucun Client trouvée pour \"$motCle\".";
            }
        }

        $this->afficherVue('client', $dVueErreur, $results);
    }

    private function ajouterUtilisateur(array $dVueEreur)
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';
        $role = $_POST['role'] ?? '';

        Validation::val_user($username, $password, $confirm, $role);

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
    public function listeUtilisateurs(array $dVueEreur)
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sousAction = $_POST['action'] ?? '';

            switch ($sousAction) {
                case 'ajouterUtilisateur':
                    $this->ajouterUtilisateur($dVueEreur);
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

        $this->afficherVue('flotte', $dVueEreur, $results);

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

        $this->afficherVue('reservation', $dVueEreur, $results);
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
        $moisFr = [
            1 => 'Janvier',
            2 => 'Février',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Août',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Décembre'
        ];

        $currentMonthLabel = $moisFr[$month] . " " . $year;



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

            $eventDepart = [
                'type' => 'depart',
                'label' => 'Départ ' . $c['Marque'] . ' ' . $c['Nom'],
                'idContrat' => $c['idContrat'] ?? null,
                'client' => $c['Client'] ?? 'Inconnu',
                'vehicule' => $c['Marque'] . ' ' . $c['Nom'],
                'dateDebut' => $c['DateDebut'],
                'dateFin' => $c['DateFin'],
                'statut' => $c['Statut'] ?? 'Actif'
            ];

            $eventRetour = [
                'type' => 'retour',
                'label' => 'Retour ' . $c['Marque'] . ' ' . $c['Nom'],
                'idContrat' => $c['idContrat'] ?? null,
                'client' => $c['Client'] ?? 'Inconnu',
                'vehicule' => $c['Marque'] . ' ' . $c['Nom'],
                'dateDebut' => $c['DateDebut'],
                'dateFin' => $c['DateFin'],
                'statut' => $c['Statut'] ?? 'Actif'
            ];


            if (isset($calendar[$c['DateDebut']])) {
                $calendar[$c['DateDebut']]['events'][] = $eventDepart;
            }
            if (isset($calendar[$c['DateFin']])) {
                $calendar[$c['DateFin']]['events'][] = $eventRetour;
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

        $this->afficherVue('planning', $dVueEreur, $results);
    }
}
?>