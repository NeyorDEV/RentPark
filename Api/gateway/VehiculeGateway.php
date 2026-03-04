<?php
namespace modeleApi\Gateway;

use modeleApi\Connection;

class VehiculeGateway {
    private $conn;

    public function __construct(Connection $conn) {
        $this->conn = $conn;
    }

    public function getAll($filters = []) {
        $sql = "SELECT * FROM Vehicule";
        $params = [];

        if (!empty($filters['nom'])) {
            $sql .= " WHERE Nom LIKE :nom";
            $params[':nom'] = ['%' . $filters['nom'] . '%', \PDO::PARAM_STR];
        }

        $this->conn->executeQuery($sql, $params);
        return $this->conn->getResults();
    }

    public function getById($numSerie) {
        $sql = "SELECT * FROM Vehicule WHERE NumSerie = :numSerie";
        $params = [':numSerie' => [$numSerie, \PDO::PARAM_STR]];

        $this->conn->executeQuery($sql, $params);
        $result = $this->conn->getResults();
        return $result ? $result[0] : null;
    }

    public function insert($data) {
        $sql = "INSERT INTO Vehicule (
                    NumSerie, Energie, NbPlaces, Categorie, Transmission, Boite, Etat,
                    Puissance, DateAchat, DateExpirationControleTech, DateDernierControleTech,
                    Marque, Nom, Annee, IdAssureur, IdFournisseur, ImagePath, Couleur, Prix
                ) VALUES (
                    :numSerie, :energie, :nbPlaces, :categorie, :transmission, :boite, :etat,
                    :puissance, :dateAchat, :dateExpiration, :dateDernierControle,
                    :marque, :nom, :annee, :idAssureur, :idFournisseur, :imagePath, :couleur, :prix
                )";

        $params = [
            ':numSerie' => [$data['NumSerie'], \PDO::PARAM_STR],
            ':energie' => [$data['Energie'], \PDO::PARAM_STR],
            ':nbPlaces' => [$data['NbPlaces'], \PDO::PARAM_INT],
            ':categorie' => [$data['Categorie'], \PDO::PARAM_STR],
            ':transmission' => [$data['Transmission'] ?? 'Traction', \PDO::PARAM_STR],
            ':boite' => [$data['Boite'] ?? 'Manuelle', \PDO::PARAM_STR],
            ':etat' => [$data['Etat'] ?? 'Libre', \PDO::PARAM_STR],
            ':puissance' => [$data['Puissance'], \PDO::PARAM_STR],
            ':dateAchat' => [$data['DateAchat'], \PDO::PARAM_STR],
            ':dateExpiration' => [$data['DateExpirationControleTech'], \PDO::PARAM_STR],
            ':dateDernierControle' => [$data['DateDernierControleTech'], \PDO::PARAM_STR],
            ':marque' => [$data['Marque'], \PDO::PARAM_STR],
            ':nom' => [$data['Nom'], \PDO::PARAM_STR],
            ':annee' => [$data['Annee'], \PDO::PARAM_INT],
            ':idAssureur' => [$data['IdAssureur'], \PDO::PARAM_INT],
            ':idFournisseur' => [$data['IdFournisseur'], \PDO::PARAM_INT],
            ':imagePath' => [$data['ImagePath'] ?? '', \PDO::PARAM_STR],
            ':couleur' => [$data['Couleur'] ?? null, \PDO::PARAM_STR],
            ':prix' => [$data['Prix'] ?? null, \PDO::PARAM_STR]
        ];

        $this->conn->executeQuery($sql, $params);
    }

    public function update($numSerie, $data) {
        $sql = "UPDATE Vehicule SET 
                    Nom = :nom,
                    Marque = :marque,
                    Annee = :annee,
                    Energie = :energie,
                    NbPlaces = :nbPlaces,
                    Categorie = :categorie,
                    Transmission = :transmission,
                    Boite = :boite,
                    Puissance = :puissance,
                    Etat = :etat,
                    Prix = :prix,
                    DateAchat = :dateAchat,
                    DateDernierControleTech = :dateDernierControle,
                    DateExpirationControleTech = :dateExpiration,
                    Couleur = :couleur,
                    ImagePath = :imagePath,
                    IdAssureur = :idAssureur,
                    IdFournisseur = :idFournisseur
                WHERE NumSerie = :numSerie";

        $params = [
            ':nom' => [$data['Nom'] ?? null, \PDO::PARAM_STR],
            ':marque' => [$data['Marque'] ?? null, \PDO::PARAM_STR],
            ':annee' => [$data['Annee'] ?? null, \PDO::PARAM_INT],
            ':energie' => [$data['Energie'] ?? null, \PDO::PARAM_STR],
            ':nbPlaces' => [$data['NbPlaces'] ?? 0, \PDO::PARAM_INT],
            ':categorie' => [$data['Categorie'] ?? null, \PDO::PARAM_STR],
            ':transmission' => [$data['Transmission'] ?? null, \PDO::PARAM_STR],
            ':boite' => [$data['Boite'] ?? null, \PDO::PARAM_STR],
            ':puissance' => [$data['Puissance'] ?? null, \PDO::PARAM_STR],
            ':etat' => [$data['Etat'] ?? null, \PDO::PARAM_STR],
            ':prix' => [$data['Prix'] ?? 0, \PDO::PARAM_STR],
            ':dateAchat' => [$data['DateAchat'] ?? null, \PDO::PARAM_STR],
            ':dateDernierControle' => [$data['DateDernierControleTech'] ?? null, \PDO::PARAM_STR],
            ':dateExpiration' => [$data['DateExpirationControleTech'] ?? null, \PDO::PARAM_STR],
            ':couleur' => [$data['Couleur'] ?? null, \PDO::PARAM_STR],
            ':imagePath' => [$data['ImagePath'] ?? null, \PDO::PARAM_STR],
            ':idAssureur' => [$data['IdAssureur'] ?? 0, \PDO::PARAM_INT],
            ':idFournisseur' => [$data['IdFournisseur'] ?? 0, \PDO::PARAM_INT],
            ':numSerie' => [$numSerie, \PDO::PARAM_STR]
        ];

        $this->conn->executeQuery($sql, $params);
    }

    public function delete($numSerie) {
        $sql = "DELETE FROM Vehicule WHERE NumSerie = :numSerie";
        $params = [':numSerie' => [$numSerie, \PDO::PARAM_STR]];
        $this->conn->executeQuery($sql, $params);
    }
}