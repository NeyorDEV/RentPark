<?php

namespace App;
use App\DTO\CreateVehiculeDTO;
use App\DTO\UpdateVehiculeDTO;
class VehiculeRepository
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function findAll(?string $nom = null)
    {
        $sql = "SELECT * FROM Vehicule";
        $params = [];

        if ($nom) {
            $sql .= " WHERE Nom LIKE :nom";
            $params[':nom'] = ['%' . $nom . '%', \PDO::PARAM_STR];
        }

        $this->conn->executeQuery($sql, $params);
        return $this->conn->getResults();
    }

    public function findByNumSerie(string $numSerie)
    {
        $this->conn->executeQuery(
            "SELECT * FROM Vehicule WHERE NumSerie = :numSerie",
            [':numSerie' => [$numSerie, \PDO::PARAM_STR]]
        );

        return $this->conn->getResults();
    }

    public function create(CreateVehiculeDTO $dto)
{
    $sql = "INSERT INTO Vehicule (
        NumSerie, Energie, NbPlaces, Categorie, Transmission, Boite, Etat, 
        Puissance, DateAchat, DateExpirationControleTech, DateDernierControleTech, 
        Marque, Nom, Annee, IdAssureur, IdFournisseur, ImagePath, Couleur, Prix
    ) VALUES (
        :numSerie, :energie, :nbPlaces, :categorie, :transmission, :boite, :etat, 
        :puissance, :dateAchat, :dateExpiration, :dateDernierControle, 
        :marque, :nom, :annee, :idAssureur, :idFournisseur, :imagePath, :couleur, :prix
    )";

    // On récupère les valeurs depuis l'objet DTO
    $params = [
        ':numSerie'            => [$dto->NumSerie, \PDO::PARAM_STR],
        ':energie'             => [$dto->Energie, \PDO::PARAM_STR],
        ':nbPlaces'            => [$dto->NbPlaces, \PDO::PARAM_INT],
        ':categorie'           => [$dto->Categorie, \PDO::PARAM_STR],
        ':transmission'        => [$dto->Transmission ?? 'Traction', \PDO::PARAM_STR],
        ':boite'               => [$dto->Boite ?? 'Manuelle', \PDO::PARAM_STR],
        ':etat'                => [$dto->Etat ?? 'Libre', \PDO::PARAM_STR],
        ':puissance'           => [$dto->Puissance, \PDO::PARAM_STR],
        ':dateAchat'           => [$dto->DateAchat ?? null, \PDO::PARAM_STR],
        ':dateExpiration'      => [$dto->DateExpirationControleTech ?? null, \PDO::PARAM_STR],
        ':dateDernierControle' => [$dto->DateDernierControleTech ?? null, \PDO::PARAM_STR],
        ':marque'              => [$dto->Marque, \PDO::PARAM_STR],
        ':nom'                 => [$dto->Nom, \PDO::PARAM_STR],
        ':annee'               => [$dto->Annee, \PDO::PARAM_INT],
        ':idAssureur'          => [$dto->IdAssureur, \PDO::PARAM_INT],
        ':idFournisseur'       => [$dto->IdFournisseur, \PDO::PARAM_INT],
        ':imagePath'           => [$dto->ImagePath ?? '', \PDO::PARAM_STR],
        ':couleur'             => [$dto->Couleur ?? null, \PDO::PARAM_STR],
        ':prix'                => [$dto->Prix ?? null, \PDO::PARAM_STR]
    ];

    $this->conn->executeQuery($sql, $params);
}

    public function delete(string $numSerie)
    {
        $this->conn->executeQuery(
            "DELETE FROM Vehicule WHERE NumSerie = :numSerie",
            [':numSerie' => [$numSerie, \PDO::PARAM_STR]]
        );
    }

    public function findUserByUsername(string $username) 
    {
        
        $this->conn->executeQuery(
            "SELECT id, username, password, role FROM users WHERE username = :username",
            [':username' => [$username, \PDO::PARAM_STR]]
        );
        
        $results = $this->conn->getResults();
        
        // On retourne le premier utilisateur trouvé ou null
        return !empty($results) ? $results[0] : null;
    }

    public function update(UpdateVehiculeDTO $dto): bool 
{
    $sql = "UPDATE Vehicule SET 
                Prix = :prix, 
                Energie = :energie, 
                Boite = :boite, 
                NbPlaces = :places, 
                Categorie = :cat, 
                Transmission = :trans, 
                Etat = :etat, 
                Puissance = :puissance, 
                Couleur = :couleur,
                ImagePath = :img
            WHERE NumSerie = :numSerie";

    $params = [
        ':numSerie'  => [$dto->NumSerie, \PDO::PARAM_STR],
        ':prix'      => [$dto->Prix, \PDO::PARAM_STR], // Souvent stocké en string/decimal
        ':energie'   => [$dto->Energie, \PDO::PARAM_STR],
        ':boite'     => [$dto->Boite, \PDO::PARAM_STR],
        ':places'    => [$dto->NbPlaces, \PDO::PARAM_INT],
        ':cat'       => [$dto->Categorie, \PDO::PARAM_STR],
        ':trans'     => [$dto->Transmission, \PDO::PARAM_STR],
        ':etat'      => [$dto->Etat, \PDO::PARAM_STR],
        ':puissance' => [$dto->Puissance, \PDO::PARAM_STR],
        ':couleur'   => [$dto->Couleur, \PDO::PARAM_STR],
        ':img'       => [$dto->ImagePath, \PDO::PARAM_STR]
    ];

    return $this->conn->executeQuery($sql, $params);
}
}