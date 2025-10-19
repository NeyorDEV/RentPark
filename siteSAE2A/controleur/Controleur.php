<?php
namespace controleur;
use modele\Connection;
use modele\UserGateway;
use modele\VehicleGateway;
use modele\ReservationGateway;
use modele\User;
use config\Validation;
class Controleur
{
    private Connection $connection;
    private VehicleGateway $gateway;
    private UserGateway $userGateway;
    private ReservationGateway $reservationGateway;
    
    public function __construct()
    {
        global $rep, $vues, $user, $pass, $dsn;

        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
  
        

        $dVueEreur = [];

        try {
            // ⚡ Instanciation de la connexion
            $this->connection = new Connection($dsn, $user, $pass);

            // ⚡ Instanciation de la Gateway
            $this->gateway = new VehicleGateway($this->connection);
            $this->userGateway = new UserGateway($this->connection);
            $this->reservationGateway = new ReservationGateway($this->connection);


            // Action
            $action = $_REQUEST['action'] ?? null;

            switch ($action) {
                case null:
                    $this->listeVoitures($dVueEreur);
                    break;

                case "ajouterVoiture":
                    $this->ajouterVoiture($dVueEreur);
                    break;

                case "supprimerVoiture":
                    $this->supprimerVoiture($dVueEreur);
                    break;

                case "inscription":
                    $this->inscription( $dVueEreur);
                    break;

                case "rechercherVoitures":
                    $this->rechercherVoitures($dVueEreur);
                    break;

                case "ajouterUtilisateur":
                    $this->ajouterUtilisateur( $dVueEreur);
                    break;

                case "supprimerUtilisateur":
                    $this->supprimerUtilisateur( $dVueEreur);
                    break;

                case "rechercherUtilisateur":
                    $this->rechercherUtilisateur($dVueEreur);
                    break;

                case "listeUtilisateur":
                    $this->listeUtilisateur($dVueEreur);
                    break;

                case 'rechercherReservation':
                    $this->rechercherReservation();
                    break;
                case 'ajouterReservation':
                    $this->ajouterReservation($_POST);
                    break;
                case 'modifierReservation':
                    $this->modifierReservation($_POST);
                    break;
                case 'supprimerReservation':
                    $this->supprimerReservation($_POST);
                    break;

                default:
                    $dVueEreur[] = "Action inconnue";
                    $this->afficherVue('flotte', $dVueEreur,$results=null,'admin');
                    break;
            }

        } catch (\PDOException $e) {
            $dVueEreur[] = "Erreur BDD : " . $e->getMessage();
            $this->afficherVue('erreur', $dVueEreur, $results = null,'admin');
        }

        exit(0);
    }

    private function listeVoitures(array $dVueEreur)
    {
        $results = $this->gateway->getAll();
        $this->afficherVue('flotte', $dVueEreur, $results,'admin');
    }
// ajouter les vue erreur et les vérif 

    private function ajouterVoiture(array $dVueEreur)
    {
        $modele    = $_POST['modele'] ?? '';
        $couleur   = $_POST['couleur'] ?? '';
        $puissance = $_POST['puissance'] ?? '';

        Validation::val_voiture($modele, $couleur, $puissance, $dVueEreur);

        if (empty($dVueEreur)) {
            $this->gateway->add(0, $modele, $couleur, $puissance);
            header("Location: index.php");
            exit;
        }

        $results = $this->gateway->getAll();
        $this->afficherVue('flotte', $dVueEreur, $results);
    }

    private function supprimerVoiture(array $dVueEreur)
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id >= 0) {             // à revoir plus tard 
            $this->gateway->delete($id);
        }

        header("Location: index.php");
        exit;
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
        $confirm  = $_POST['confirm'] ?? '';
        $role     = $_POST['role'] ?? '';

        
        Validation::val_user($username, $password, $confirm, $role, $dVueErreur);

        if (!empty($dVueErreur)) {
            require '../view/viewErreur.php';
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        
        $user = new User(null, $username, $hashedPassword, $role);
        $this->userGateway->addUser($user);

        
        $results = $this->gateway->getAll();
        require __DIR__ . '/../view/viewVoiture.php';
    }

    private function afficherVue(string $vueKey, array $dVueErreur, ?array $results = null, string $role = 'guest'): void

    {
        global $rep, $vues;

        if (!isset($vues[$vueKey])) {
            echo "Vue '$vueKey' non définie.";
            exit;
        }

        $cheminVue = realpath($rep . $vues[$vueKey]);

        if ($cheminVue && file_exists($cheminVue)) {
            require($cheminVue);
        } else {
            echo "Fichier de vue introuvable : " . ($rep . $vues[$vueKey]);
            exit;
        }
    }
    private function rechercherUtilisateur(array $dVueErreur = []): void
    {
        $motCle = trim($_GET['q'] ?? '');

        if ($motCle === '') {
            $results = $this->userGateway->getAllUser();
        } 
        else {
            $results = $this->userGateway->rechercherUtilisateur($motCle);

            if (empty($results)) {
                $dVueErreur[] = "Aucun utilisateur trouvée pour \"$motCle\".";
            }
        }

        $this->afficherVue('user', $dVueErreur, $results, 'admin');
    }

    private function supprimerUtilisateur(array $dVueEreur)
    {
        $id = (int)($_POST['id'] ?? -1);
        if ($id >= 0) {
            $this->userGateway->deleteUser($id);
        }

        header("Location: index.php");
        exit;
    }

    private function ajouterUtilisateur(array $dVueEreur)
    {
        $username   = $_POST['username'] ?? '';
        $password   = $_POST['password'] ?? '';
        $confirm    = $_POST['confirm'] ?? '';
        $role = $_POST['role'] ?? '';

        Validation::val_user($username, $password, $confirm, $role, $dVueEreur);

        if (empty($dVueEreur)) {
            $this->userGateway->addUser( $username, $password, $role);
            header("Location: index.php");
            exit;
        }

        $results = $this->userGateway->getAllUser();
        $this->afficherVue('user', $dVueEreur, $results);
    }

    private function listeUtilisateur(array $dVueEreur)
    {
        $results = $this->userGateway->getAllUser();
        $this->afficherVue('user', $dVueEreur, $results,'admin');
    }

    private function rechercherReservation(): void
    {
        $champ  = $_GET['champ']  ?? 'idContrat';
        $q      = trim($_GET['q'] ?? '');
        $filtre = $_GET['filtre'] ?? 'en-cours';

        // whitelist des champs autorisés
        $allowed = ['idContrat' => 'idContrat', 'Vehicule' => 'Vehicule', 'Client' => 'Client'];
        if (!isset($allowed[$champ])) { $champ = 'idContrat'; }

        $results = $this->reservationGateway->searchReservations($champ, $q, $filtre);

        $this->afficherVue('reservation', [], $results, 'admin');
    }
    private function ajouterReservation(array $post): void {
        $vehicule  = trim($post['Vehicule']  ?? '');
        $client    = (int)($post['Client']   ?? 0);
        $dateDebut = trim($post['DateDebut'] ?? '');
        $dateFin   = trim($post['DateFin']   ?? '');

        $err = [];

        if ($vehicule === '') $err[] = "Le véhicule (VIN) est obligatoire.";
        if ($client <= 0)     $err[] = "Le client (ID) doit être un entier positif.";
        if ($dateDebut === '') $err[] = "La date de début est obligatoire.";
        if ($dateFin   === '') $err[] = "La date de fin est obligatoire.";

        $d1 = \DateTime::createFromFormat('Y-m-d', $dateDebut) ?: null;
        $d2 = \DateTime::createFromFormat('Y-m-d', $dateFin)   ?: null;
        if (!$d1 || !$d2) {
            $err[] = "Format de date invalide (attendu : AAAA-MM-JJ).";
        } elseif ($d1 > $d2) {
            $err[] = "La date de début doit être antérieure ou égale à la date de fin.";
        }

        // 👉 INSERT SI OK
        if (empty($err)) {
            try {
                $this->reservationGateway->insertReservation($vehicule, $client, $dateDebut, $dateFin);
                // (optionnel) message de succès via la vue
                // $msg = "Réservation créée avec succès.";
            } catch (\PDOException $e) {
                $err[] = "Erreur base de données : " . $e->getMessage();
            }
        }

        // Réaffiche la page (liste + form) avec éventuellement les erreurs
        $this->rechercherReservation(); 
    }

    private function modifierReservation(array $post): void
    {
        $id        = (int)($post['id'] ?? 0);
        $vehicule  = trim($post['Vehicule']  ?? '');
        $client    = (int)($post['Client']   ?? 0);
        $dateDebut = trim($post['DateDebut'] ?? '');
        $dateFin   = trim($post['DateFin'] ?? '');

        $err = [];

        if ($id <= 0) $err[] = "Identifiant de contrat invalide.";
        if ($vehicule === '') $err[] = "Le véhicule (VIN) est obligatoire.";
        if ($client <= 0)     $err[] = "Le client (ID) doit être un entier positif.";
        if ($dateDebut === '') $err[] = "La date de début est obligatoire.";
        if ($dateFin   === '') $err[] = "La date de fin est obligatoire.";

        $d1 = \DateTime::createFromFormat('Y-m-d', $dateDebut) ?: null;
        $d2 = \DateTime::createFromFormat('Y-m-d', $dateFin)   ?: null;
        if (!$d1 || !$d2) {
            $err[] = "Format de date invalide (attendu : AAAA-MM-JJ).";
        } elseif ($d1 > $d2) {
            $err[] = "La date de début doit être antérieure ou égale à la date de fin.";
        }

        if (empty($err)) {
            try {
                $this->reservationGateway->update($id, $vehicule, $client, $dateDebut, $dateFin);
                // redirige vers la liste des réservations
                header('Location: index.php?action=rechercherReservation');
                exit;
            } catch (\PDOException $e) {
                $err[] = "Erreur base de données : " . $e->getMessage();
            }
        }

        $results = $this->reservationGateway->searchReservations('idContrat', '', 'toutes');
        $this->afficherVue('reservation', $err, $results, 'admin');
    }

    private function supprimerReservation(array $dVueEreur)
    {
        $id = (int)($_POST['id'] ?? -1);
        if ($id >= 0) {
            $this->reservationGateway->delete($id);
        }
        else{
            $dVueErreur[] = "Cette réservation n'existe pas, impossible de la supprimer .";
        }
        
        $this->rechercherReservation();
        exit;
    }

}

?>