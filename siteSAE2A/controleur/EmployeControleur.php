<?php
namespace controleur;
use modele\Connection;
use modele\VehicleGateway;
use modele\ReservationGateway;
use modele\UserGateway;
use GuzzleHttp\Client;
use config\Validation;
use GuzzleHttp\Exception\RequestException;

require_once __DIR__ . '/ApiHelper.php';

class EmployeControleur
{
    use RoleAwareTrait;

    private Connection $connection;
    private VehicleGateway $gateway;
    private UserGateway $userGateway;
    private ReservationGateway $reservationGateway;
    private Client $apiClient;
    private Client $clientApiClient;

    private Client $contratApiClient; 

    public function __construct()
    {
        global $rep, $vues, $user, $pass, $dsn, $action;
        $this->checkEmploye();
        $dVueErreur = [];

        try {
            $this->connection         = new Connection($dsn, $user, $pass);
            $this->gateway            = new VehicleGateway($this->connection);
            $this->userGateway        = new UserGateway($this->connection);
            $this->reservationGateway = new ReservationGateway($this->connection);
            $this->apiClient          = getApiClient();
            $this->clientApiClient    = getClientApiClient();
            $this->contratApiClient   = getContratApiClient();

            switch ($action) {
                case "affichePlanning":    $this->affichePlanning($dVueErreur); break;
                case "listeClients":       $this->listeClients($dVueErreur); break;
                case "listeUtilisateurs":  $this->listeUtilisateurs($dVueErreur); break;
                case 'listeReservation':   $this->listeReservation($dVueErreur); break;
                case 'listeVoitures':      $this->listeVoitures($dVueErreur); break;
                default:
                    $dVueErreur[] = "Action inconnue";
                    $this->afficherVue('homeCustomers', $dVueErreur, null);
                    break;
            }
        } catch (\PDOException $e) {
            $dVueErreur[] = "Erreur BDD : " . $e->getMessage();
            $this->afficherVue('erreur', $dVueErreur);
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

    private function ajouterVoiture(array $dVueErreur)
    {
        $imagePath = '';

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $nomFichier = uniqid() . '_' . basename($_FILES['image']['name']);
            $dossier    = __DIR__ . '/../html/icons/' . $nomFichier;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dossier)) {
                $imagePath = 'html/icons/' . $nomFichier;
            }
        }

        try {
            $this->apiClient->post("api/vehicules", $this->withAuth([
                'json' => [
                    'NumSerie'                   => $_POST['NumSerie'],
                    'Energie'                    => $_POST['Energie'],
                    'NbPlaces'                   => (int)$_POST['NbPlaces'],
                    'Categorie'                  => $_POST['Categorie'],
                    'Transmission'               => $_POST['Transmission'],
                    'Boite'                      => $_POST['Boite'],
                    'Etat'                       => $_POST['Etat'] ?? 'Libre',
                    'Puissance'                  => $_POST['Puissance'],
                    'Marque'                     => $_POST['Marque'],
                    'Nom'                        => $_POST['Nom'],
                    'Annee'                      => (int)$_POST['Annee'],
                    'IdAssureur'                 => (int)$_POST['IdAssureur'],
                    'IdFournisseur'              => (int)$_POST['IdFournisseur'],
                    'Prix'                       => $_POST['Prix'] ?? null,
                    'DateAchat'                  => $_POST['DateAchat'],
                    'DateExpirationControleTech' => $_POST['DateExpirationControleTech'],
                    'DateDernierControleTech'    => $_POST['DateDernierControleTech'],
                    'ImagePath'                  => $imagePath,
                    'Couleur'                    => $_POST['Couleur'] ?? null,
                ]
            ]));
        } catch (RequestException $e) {
            $dVueErreur[] = "Erreur création véhicule : " . $e->getMessage();
            $this->afficherVue('flotte', $dVueErreur, []);
            return;
        }

        header("Location: /siteSAE2A/voitures");
        exit;
    }

    private function supprimerVoiture(array $dVueErreur)
    {
        $id = $_POST['NumSerie'] ?? -1;
        try {
            $this->apiClient->delete("api/vehicules/$id", $this->authHeaders());
        } catch (RequestException $e) {
            $dVueErreur[] = "Erreur suppression : " . $e->getMessage();
        }
        header("Location: /siteSAE2A/voitures");
        exit;
    }

    private function modifierVoiture(array $dVueErreur)
    {
        $numSerie = $_POST['NumSerie'] ?? '';
        $data = [
            'Prix'                       => $_POST['Prix']                       ?? null,
            'Energie'                    => $_POST['Energie']                    ?? null,
            'Boite'                      => $_POST['Boite']                      ?? null,
            'Etat'                       => $_POST['Etat']                       ?? null,
            'Couleur'                    => $_POST['Couleur']                    ?? null,
            'Puissance'                  => $_POST['Puissance']                  ?? null,
            'Categorie'                  => $_POST['Categorie']                  ?? null,
            'Transmission'               => $_POST['Transmission']               ?? null,
            'DateExpirationControleTech' => $_POST['DateExpirationControleTech'] ?? null,
            'DateDernierControleTech'    => $_POST['DateDernierControleTech']    ?? null,
        ];

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $nomFichier = uniqid() . '_' . basename($_FILES['image']['name']);
            $dossier    = __DIR__ . '/../html/icons/' . $nomFichier;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dossier)) {
                $data['ImagePath'] = 'html/icons/' . $nomFichier;
            }
        }

        try {
            $this->apiClient->put("api/vehicules/$numSerie", $this->withAuth(['json' => $data]));
        } catch (RequestException $e) {
            $dVueErreur[] = "Erreur modification : " . $e->getMessage();
        }

        header("Location: /siteSAE2A/voitures");
        exit;
    }

    private function rechercherVoitures(array $dVueErreur = []): void
    {
        $motCle = trim($_GET['q'] ?? '');
        try {
            $options  = $motCle !== '' ? $this->withAuth(['query' => ['nom' => $motCle]]) : $this->authHeaders();
            $response = $this->apiClient->get('api/vehicules', $options);
            $results  = json_decode($response->getBody()->getContents(), true) ?? [];
            if (empty($results)) $dVueErreur[] = "Aucune voiture trouvée pour \"$motCle\".";
        } catch (RequestException $e) {
            $dVueErreur[] = "Erreur recherche : " . $e->getMessage();
            $results = [];
        }
        $this->afficherVue('flotte', $dVueErreur, $results);
    }

    public function listeVoitures(array $dVueErreur)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sousAction = $_POST['action'] ?? '';
            switch ($sousAction) {
                case 'ajouterVoiture':   $this->ajouterVoiture($dVueErreur);  break;
                case 'supprimerVoiture': $this->supprimerVoiture($dVueErreur); break;
                case 'modifierVoiture':  $this->modifierVoiture($dVueErreur);  break;
            }
            header("Location: /siteSAE2A/voitures");
            exit;
        }

        $sousAction = $_GET['action'] ?? '';
        if ($sousAction === 'rechercherVoitures') {
            $this->rechercherVoitures($dVueErreur);
            return;
        }

        try {
            $response = $this->apiClient->get('api/vehicules', $this->authHeaders());
            $results  = json_decode($response->getBody()->getContents(), true) ?? [];
        } catch (RequestException $e) {
            $dVueErreur[] = "Impossible de récupérer les véhicules.";
            $results = [];
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
            if (empty($results)) $dVueErreur[] = "Aucun utilisateur trouvé pour \"$motCle\".";
        }
        $this->afficherVue('user', $dVueErreur, $results);
    }

    private function rechercherClient(array $dVueErreur = []): void
    {
        $motCle  = trim($_GET['q'] ?? '');
        $results = [];

        try {
            if ($motCle === '') {
                $response = $this->apiClient->get('clients', $this->authHeaders());
            } else {
                $response = $this->apiClient->get('clients', $this->withAuth(['query' => ['q' => $motCle]]));
            }
            $results = json_decode($response->getBody()->getContents(), true) ?? [];
            if (empty($results)) $dVueErreur[] = "Aucun client trouvé pour \"$motCle\".";
        } catch (RequestException $e) {
            $dVueErreur[] = "Erreur recherche client : " . $e->getMessage();
        }

        $this->afficherVue('client', $dVueErreur, $results);
    }

    private function ajouterUtilisateur(array $dVueErreur)
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm']  ?? '';
        $role     = $_POST['role']     ?? '';

        Validation::val_user($username, $password, $confirm, $role);

        if (empty($dVueErreur)) {
            $this->userGateway->addUser($username, $password, $role);
            header("Location: /siteSAE2A/utilisateurs");
            exit;
        }

        $results = $this->userGateway->getAllUser();
        $this->afficherVue('user', $dVueErreur, $results);
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
                $this->apiClient->post("client", $this->withAuth(['json' => $data]));
                header("Location: /siteSAE2A/clients");
                exit;
            } catch (RequestException $e) {
                $dVueErreur[] = "Erreur ajout client : " . $e->getMessage();
            }
        }

        $response = $this->apiClient->get('clients', $this->authHeaders());
        $results  = json_decode($response->getBody()->getContents(), true) ?? [];
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
                $this->apiClient->put("client/$IdClient", $this->withAuth(['json' => $data]));
                header("Location: /siteSAE2A/clients");
                exit;
            } catch (RequestException $e) {
                $dVueErreur[] = "Erreur modification client : " . $e->getMessage();
            }
        }

        $response = $this->apiClient->get('clients', $this->authHeaders());
        $results  = json_decode($response->getBody()->getContents(), true) ?? [];
        $this->afficherVue('client', $dVueErreur, $results);
    }

    public function listeUtilisateurs(array $dVueErreur)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sousAction = $_POST['action'] ?? '';
            if ($sousAction === 'ajouterUtilisateur') $this->ajouterUtilisateur($dVueErreur);
            header("Location: /siteSAE2A/utilisateurs");
            exit;
        }

        $sousAction = $_GET['action'] ?? '';
        if ($sousAction === 'rechercherUtilisateur') {
            $this->rechercherUtilisateur($dVueErreur);
            return;
        }

        $results = $this->userGateway->getAllUser();
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

    private function rechercherReservation(array &$dVueErreur = []): void
    {
        $champ  = $_GET['champ']  ?? 'idContrat';
        $q      = trim($_GET['q'] ?? '');
        $filtre = $_GET['filtre'] ?? 'en-cours';
        $today  = date('Y-m-d');
        $results = [];

        try { 
            $response    = $this->contratApiClient->get('api/contrat', $this->authHeaders());
            $allContrats = json_decode($response->getBody()->getContents(), true) ?? [];

            $filtered = array_filter($allContrats, function ($c) use ($champ, $q, $filtre, $today) {
                switch ($filtre) {
                    case 'a-valider': $match = ($c['Statut'] ?? '') === 'EnCoursValidation'; break;
                    case 'a-venir':   $match = ($c['DateDebut'] ?? '') > $today; break;
                    case 'passees':   $match = ($c['DateFin']   ?? '') < $today; break;
                    case 'toutes':    $match = true; break;
                    default:          $match = ($c['DateDebut'] ?? '') <= $today && ($c['DateFin'] ?? '') >= $today;
                }
                if (!$match) return false;
                if ($q !== '') {
                    $idContrat  = $c['idContrat']  ?? $c['IdContrat']  ?? '';
                    $idClient   = $c['IdClient']   ?? $c['idClient']   ?? '';
                    $idVehicule = $c['IdVehicule'] ?? $c['idVehicule'] ?? '';
                    if ($champ === 'idContrat' && (string)$idContrat  !== $q) return false;
                    if ($champ === 'Client'    && (string)$idClient   !== $q) return false;
                    if ($champ === 'Vehicule'  && stripos((string)$idVehicule, $q) === false) return false;
                }
                return true;
            });

            foreach ($filtered as $c) {
                $results[] = [
                    'idContrat'  => $c['idContrat']  ?? $c['IdContrat']  ?? null,
                    'IdClient'   => $c['IdClient']   ?? $c['idClient']   ?? null,
                    'idVehicule' => $c['IdVehicule'] ?? $c['idVehicule'] ?? null,
                    'DateDebut'  => $c['DateDebut']  ?? null,
                    'DateFin'    => $c['DateFin']    ?? null,
                    'Statut'     => $c['Statut']     ?? null,
                ];
            }
        } catch (RequestException $e) {
            $dVueErreur[] = "Impossible de récupérer les contrats.";
        }

        $this->afficherVue('reservation', $dVueErreur, $results);
    }

    private function ajouterReservation(array $post, array &$dVueErreur): void
    {
        try {
            $this->contratApiClient->post('api/contrat', $this->withAuth([
                'json' => [
                    'DateDebut'  => trim($post['DateDebut']  ?? ''),
                    'DateFin'    => trim($post['DateFin']    ?? ''),
                    'IdVehicule' => trim($post['Vehicule']   ?? ''),
                    'IdClient'   => (int)($post['Client']   ?? 0),
                    'Statut'     => 'EnCoursValidation'
                ]
            ]));
        } catch (RequestException $e) {
            $dVueErreur[] = "Erreur ajout réservation : " . $e->getMessage();
        }
        $this->rechercherReservation($dVueErreur);
    }

    private function modifierReservation(array $post, array &$dVueErreur): void
    {
        $id = (int)($post['id'] ?? 0);
        try {
            $this->apiClient->put("api/contrat/$id", $this->withAuth([
                'json' => [
                    'DateDebut' => trim($post['DateDebut'] ?? ''),
                    'DateFin'   => trim($post['DateFin']   ?? ''),
                    'Vehicule'  => trim($post['Vehicule']  ?? ''),
                    'Client'    => (int)($post['Client']  ?? 0),
                ]
            ]));
            header('Location: /siteSAE2A/reservation');
            exit;
        } catch (RequestException $e) {
            $dVueErreur[] = "Erreur modification réservation : " . $e->getMessage();
        }
        $this->rechercherReservation($dVueErreur);
    }

    private function supprimerReservation(array &$dVueErreur): void
    {
        $id = (int)($_POST['id'] ?? -1);
        if ($id > 0) {
            try {
                $this->contratApiClient->delete("api/contrat/$id", $this->authHeaders());
            } catch (RequestException $e) {
                $dVueErreur[] = "Erreur suppression réservation : " . $e->getMessage();
            }
        } else {
            $dVueErreur[] = "ID invalide.";
        }
        $this->rechercherReservation($dVueErreur);
    }

    private function changerStatutReservation(array $post, array &$dVueErreur): void
    {
        $id            = (int)($post['id'] ?? 0);
        $nouveauStatut = trim($post['nouveauStatut'] ?? '');

        if ($id > 0 && in_array($nouveauStatut, ['Validé', 'Annulé'])) {
            try {
                $this->contratApiClient->patch("api/contrat/$id/statut", $this->withAuth([
                    'json' => ['Statut' => $nouveauStatut]
                ]));
            } catch (RequestException $e) {
                $dVueErreur[] = "Erreur changement statut : " . $e->getMessage();
            }
        } else {
            $dVueErreur[] = "Données invalides pour le changement de statut.";
        }
        $this->rechercherReservation($dVueErreur);
    }

    public function listeReservation(array &$dVueErreur)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sousAction = $_POST['action'] ?? '';
            switch ($sousAction) {
                case 'ajouterReservation':   $this->ajouterReservation($_POST, $dVueErreur);   break;
                case 'modifierReservation':  $this->modifierReservation($_POST, $dVueErreur);  break;
                case 'supprimerReservation': $this->supprimerReservation($dVueErreur);          break;
                case 'changerStatut':        $this->changerStatutReservation($_POST, $dVueErreur); break;
            }
            exit;
        }
        $this->rechercherReservation($dVueErreur);
    }

    public function affichePlanning(array $dVueErreur)
    {
        $month = max(1, min(12, (int)($_GET['month'] ?? date('m'))));
        $year  = (int)($_GET['year'] ?? date('Y'));

        $prevMonth = $month - 1; $prevYear = $year;
        $nextMonth = $month + 1; $nextYear = $year;
        if ($prevMonth < 1)  { $prevMonth = 12; $prevYear--; }
        if ($nextMonth > 12) { $nextMonth = 1;  $nextYear++; }

        $moisFr = [1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',
                   7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'];
        $currentMonthLabel = $moisFr[$month] . " " . $year;

        $contracts     = $this->reservationGateway->getMonthlyPlanning();
        $daysInMonth   = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $today         = date('Y-m-d');
        $calendar      = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $calendar[$date] = ['label' => $day, 'isToday' => ($date === $today), 'events' => []];
        }

        foreach ($contracts as $c) {
            $base = [
                'idContrat' => $c['idContrat'] ?? null,
                'client'    => $c['Client']    ?? 'Inconnu',
                'vehicule'  => $c['Marque'] . ' ' . $c['Nom'],
                'dateDebut' => $c['DateDebut'],
                'dateFin'   => $c['DateFin'],
                'statut'    => $c['Statut']    ?? 'Actif',
            ];
            if (isset($calendar[$c['DateDebut']])) {
                $calendar[$c['DateDebut']]['events'][] = array_merge($base, ['type'=>'depart', 'label'=>'Départ '.$c['Marque'].' '.$c['Nom']]);
            }
            if (isset($calendar[$c['DateFin']])) {
                $calendar[$c['DateFin']]['events'][]   = array_merge($base, ['type'=>'retour', 'label'=>'Retour '.$c['Marque'].' '.$c['Nom']]);
            }
        }

        $this->afficherVue('planning', $dVueErreur, [
            'calendar'          => $calendar,
            'prevMonth'         => $prevMonth,
            'prevYear'          => $prevYear,
            'nextMonth'         => $nextMonth,
            'nextYear'          => $nextYear,
            'currentMonthLabel' => $currentMonthLabel,
        ]);
    }
}