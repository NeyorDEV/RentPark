<?php
class Controleur
{
    private Connection $connection;
    private VehicleGateway $gateway;

    public function __construct()
    {
        global $rep, $vues, $user, $pass, $dsn;
        session_start();

        $dVueEreur = [];

        try {
            // ⚡ Instanciation de la connexion
            $this->connection = new Connection($dsn, $user, $pass);

            // ⚡ Instanciation de la Gateway
            $this->gateway = new VehicleGateway($this->connection);

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

                default:
                    $dVueEreur[] = "Action inconnue";
                    $this->afficherVue('flotte', $dVueEreur);
                    break;
            }

        } catch (PDOException $e) {
            $dVueEreur[] = "Erreur BDD : " . $e->getMessage();
            $this->afficherVue('erreur', $dVueEreur);
        }

        exit(0);
    }

    private function listeVoitures(array $dVueEreur)
    {
        $results = $this->gateway->getAll();
        $this->afficherVue('flotte', $dVueEreur, $results,'admin');
    }

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

    /**
     * Affiche une vue de manière sécurisée
     * @param string $vueKey clé du tableau $vues
     * @param array $dVueEreur tableau d'erreurs
     * @param array|null $results tableau de résultats optionnel
     */
    private function afficherVue(string $vueKey, array $dVueEreur, array $results = null,string $role )
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
}
?>
