<?php
namespace controleur;
use modele\Connection;
use modele\VehicleGateway;

class UserController
{
    private Connection $connection;
    private VehicleGateway $gateway;

    public function __construct()
    {
        global $rep, $vues, $user, $pass, $dsn;

        $dVueErreur = [];

        try {
            $this->connection = new Connection($dsn, $user, $pass);
            $this->gateway = new VehicleGateway($this->connection);

            
            $this->listeVoitures($dVueErreur);

        } catch (\PDOException $e) {
            $dVueErreur[] = "Erreur BDD : " . $e->getMessage();
            $this->afficherVue('erreur', $dVueErreur);
        }

        exit(0);
    }

    private function listeVoitures(array $dVueErreur)
    {
        $results = $this->gateway->getAll();
        $this->afficherVue('flotte', $dVueErreur, $results, 'user');
    }

    private function afficherVue(string $vueKey, array $dVueEreur, ?array $results = null, string $role = 'admin')
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
