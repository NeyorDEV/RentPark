<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

// 1. INITIALISATION & CONFIGURATION
// -----------------------------------------------------------------------
// Autoload PSR-4
$loader = require_once __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('BL\\', __DIR__);

// Récupération de la connexion via database.php
$databaseFactory = require_once __DIR__ . '/../config/database.php';
$conn = $databaseFactory(); // $conn est maintenant une instance de Connection

$app = AppFactory::create();

$app->addBodyParsingMiddleware();



// -----------------------------------------------------------------------
// SECTION : VOITURES (Vehicule)
// -----------------------------------------------------------------------

// GET : Liste toutes les voitures
$app->get('/voitures', function (Request $request, Response $response, $args) use ($conn) {
    $params = $request->getQueryParams();
    $sql = "SELECT * FROM Vehicule";
    $queryParams = [];

    if (!empty($params['nom'])) {
        $sql .= " WHERE Nom LIKE :nom";
        $queryParams[':nom'] = ['%' . $params['nom'] . '%', \PDO::PARAM_STR];
    }
    $conn->executeQuery($sql, $queryParams);
    $result = $conn->getResults();

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/voitures/{numSerie}', function (Request $request, Response $response, $args) use ($conn) {
    $numSerie = (string) $args['numSerie'];

    $conn->executeQuery(
        "SELECT * FROM Vehicule WHERE NumSerie = :numSerie",
        [':numSerie' => [$numSerie, \PDO::PARAM_STR]]
    );

    $result = $conn->getResults();

    if (empty($result)) {
        $response->getBody()->write(json_encode(['error' => 'Véhicule non trouvé']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    // On retourne le premier (et seul) résultat
    $response->getBody()->write(json_encode($result[0]));
    return $response->withHeader('Content-Type', 'application/json');
});

// POST : Ajouter un véhicule
$app->post('/vehicule', function (Request $request, Response $response, $args) use ($conn) {

    $data = $request->getParsedBody();

    if (empty($data['NumSerie'])) {
        $response->getBody()->write(json_encode([
            'error' => 'Le champ NumSerie est obligatoire.'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

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
        ':nbPlaces' => [$data['NbPlaces'], \PDO::PARAM_STR],
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
        ':annee' => [$data['Annee'], \PDO::PARAM_STR],
        ':idAssureur' => [$data['IdAssureur'], \PDO::PARAM_INT],
        ':idFournisseur' => [$data['IdFournisseur'], \PDO::PARAM_INT],

        ':imagePath' => [$data['ImagePath'] ?? '', \PDO::PARAM_STR],

        ':couleur' => [$data['Couleur'] ?? null, \PDO::PARAM_STR],
        ':prix' => [$data['Prix'] ?? null, \PDO::PARAM_STR]
    ];

    try {
        $conn->executeQuery($sql, $params);

        $response->getBody()->write(json_encode([
            'message' => 'Véhicule ajouté avec succès',
            'NumSerie' => $data['NumSerie']
        ]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);

    } catch (\Exception $e) {
        $errorCode = $e->getCode();
        $status = 500;
        $message = 'Erreur lors de l\'ajout du véhicule : ' . $e->getMessage();

        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            $status = 409; // Conflict
            $message = 'Un véhicule avec ce Numéro de Série existe déjà.';
        }

        $response->getBody()->write(json_encode([
            'error' => $message
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
});

// PUT : Modification d'un véhicule
$app->put('/voitures/{numSerie}', function (Request $request, Response $response, $args) use ($conn) {
    $numSerie = (string) $args['numSerie'];
    $data = $request->getParsedBody();

    // Vérification basique des données (ajustez selon vos besoins)
    if (empty($data)) {
        $response->getBody()->write(json_encode(['error' => 'Aucune donnée envoyée']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

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
            DateExpirationControleTech = :dateExpirationControle,
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
        ':dateExpirationControle' => [$data['DateExpirationControleTech'] ?? null, \PDO::PARAM_STR],
        ':couleur' => [$data['Couleur'] ?? null, \PDO::PARAM_STR],
        ':imagePath' => [$data['ImagePath'] ?? null, \PDO::PARAM_STR],
        ':idAssureur' => [$data['IdAssureur'] ?? 0, \PDO::PARAM_INT],
        ':idFournisseur' => [$data['IdFournisseur'] ?? 0, \PDO::PARAM_INT],
        ':numSerie' => [$numSerie, \PDO::PARAM_STR]
    ];


    try {
        $conn->executeQuery($sql, $params);
        $response->getBody()->write(json_encode(['message' => 'Véhicule mis à jour avec succès']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([
            'error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// DELETE : Supprimer une voiture
$app->delete('/delete/voitures/{numSerie}', function ($request, $response, $args) use ($conn) {
    $numSerie = (string) $args['numSerie'];

    // 1️⃣ Vérifier si la voiture existe
    $conn->executeQuery(
        "SELECT NumSerie FROM Vehicule WHERE NumSerie = :numSerie",
        [':numSerie' => [$numSerie, \PDO::PARAM_STR]]
    );
    $voiture = $conn->getResults();

    if (empty($voiture)) {
        $response->getBody()->write(json_encode(['error' => 'Voiture non trouvée']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    // 2️⃣ Essayer de supprimer la voiture
    try {
        $conn->executeQuery(
            "DELETE FROM Vehicule WHERE NumSerie = :numSerie",
            [':numSerie' => [$numSerie, \PDO::PARAM_STR]]
        );
    } catch (\PDOException $e) {
        // 3️⃣ Gestion propre de la clé étrangère
        if ($e->getCode() === '23000') { // SQLSTATE pour contrainte FK
            $response->getBody()->write(json_encode([
                'error' => 'Impossible de supprimer la voiture',
                'message' => 'Cette voiture est utilisée dans un ou plusieurs contrats'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
        }

        // Autres erreurs SQL
        $response->getBody()->write(json_encode([
            'error' => 'Erreur SQL',
            'message' => $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    // 4️⃣ Suppression réussie
    $response->getBody()->write(json_encode(['message' => 'Voiture supprimée avec succès']));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});

// POST : Ajouter un véhicule
$app->post('/add/vehicule', function (Request $request, Response $response, $args) use ($conn) {

    $data = $request->getParsedBody();

    if (empty($data['NumSerie'])) {
        $response->getBody()->write(json_encode([
            'error' => 'Le champ NumSerie est obligatoire.'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

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
        ':nbPlaces' => [$data['NbPlaces'], \PDO::PARAM_STR],
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
        ':annee' => [$data['Annee'], \PDO::PARAM_STR],
        ':idAssureur' => [$data['IdAssureur'], \PDO::PARAM_INT],
        ':idFournisseur' => [$data['IdFournisseur'], \PDO::PARAM_INT],

        ':imagePath' => [$data['ImagePath'] ?? '', \PDO::PARAM_STR],

        ':couleur' => [$data['Couleur'] ?? null, \PDO::PARAM_STR],
        ':prix' => [$data['Prix'] ?? null, \PDO::PARAM_STR]
    ];

    try {
        $conn->executeQuery($sql, $params);

        $response->getBody()->write(json_encode([
            'message' => 'Véhicule ajouté avec succès',
            'NumSerie' => $data['NumSerie']
        ]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);

    } catch (\Exception $e) {
        $errorCode = $e->getCode();
        $status = 500;
        $message = 'Erreur lors de l\'ajout du véhicule : ' . $e->getMessage();

        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            $status = 409; // Conflict
            $message = 'Un véhicule avec ce Numéro de Série existe déjà.';
        }

        $response->getBody()->write(json_encode([
            'error' => $message
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
});

// -----------------------------------------------------------------------
// SECTION : CLIENTS
// -----------------------------------------------------------------------

// GET : Liste des clients
$app->get('/clients', function (Request $request, Response $response, $args) use ($conn) {
    $conn->executeQuery("SELECT * FROM Client");
    $client = $conn->getResults();

    $response->getBody()->write(json_encode($client));
    return $response->withHeader('Content-Type', 'application/json');
});

// PUT : Modification d'un client
$app->put('/client/{IdClient}', function (Request $request, Response $response, $args) use ($conn) {
    $IdClient = (string) $args['IdClient'];
    $data = $request->getParsedBody();

    // Vérification basique des données (ajustez selon vos besoins)
    if (empty($data)) {
        $response->getBody()->write(json_encode(['error' => 'Aucune donnée envoyée']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $sql = "UPDATE Client SET 
            Nom = :nom, 
            Prenom = :prenom, 
            Email = :email, 
            NumTel = :numTel, 
            NumPermis = :numPermis, 
            DateNaiss = :dateNaiss, 
            Nationalite = :nationalite
        WHERE IdClient = :idClient";
        $params = [
        ':nom'         => [$data['Nom'], \PDO::PARAM_STR],
        ':prenom'      => [$data['Prenom'], \PDO::PARAM_STR],
        ':email'       => [$data['Email'], \PDO::PARAM_STR],
        ':numTel'      => [$data['NumTel'] ?? null, \PDO::PARAM_STR],
        ':numPermis'   => [$data['NumPermis'], \PDO::PARAM_STR],
        ':dateNaiss'   => [$data['DateNaiss'] ?? null, \PDO::PARAM_STR],
        ':nationalite' => [$data['Nationalite'] ?? null, \PDO::PARAM_STR],
        ':idClient'    => [$IdClient, \PDO::PARAM_INT]
    ];

        

    try {
        $conn->executeQuery($sql, $params);
        $response->getBody()->write(json_encode(['message' => 'Client mis à jour avec succès']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([
            'error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// POST : Ajouter un client
$app->post('/modif/client', function (Request $request, Response $response, $args) use ($conn) {

    $data = $request->getParsedBody();

    // Validation des champs
    if (empty($data['Nom']) || empty($data['Prenom']) || empty($data['Email']) || empty($data['NumPermis'])) {
        $response->getBody()->write(json_encode([
            'error' => 'Les champs Nom, Prenom, Email et NumPermis sont obligatoires.'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $sql = "INSERT INTO Client (Nom, Prenom, Email, NumTel, NumPermis, DateNaiss, Nationalite) 
            VALUES (:nom, :prenom, :email, :numTel, :numPermis, :dateNaiss, :nationalite)";

    $params = [
        ':nom' => [$data['Nom'], \PDO::PARAM_STR],
        ':prenom' => [$data['Prenom'], \PDO::PARAM_STR],
        ':email' => [$data['Email'], \PDO::PARAM_STR],
        ':numTel' => [$data['NumTel'] ?? null, \PDO::PARAM_STR],
        ':numPermis' => [$data['NumPermis'], \PDO::PARAM_STR],
        ':dateNaiss' => [$data['DateNaiss'] ?? null, \PDO::PARAM_STR],
        ':nationalite' => [$data['Nationalite'] ?? null, \PDO::PARAM_STR]
    ];

    try {
        $conn->executeQuery($sql, $params);

        // On récupère l'ID du client qui vient d'être créé pour pouvoir l'utiliser pour le contrat
        $idClient = $conn->getLastInsertId();

        $response->getBody()->write(json_encode([
            'message' => 'Client créé avec succès',
            'idClient' => $idClient
        ]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);

    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([
            'error' => 'Erreur lors de la création du client : ' . $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// POST : Ajouter un client
$app->post('/client', function (Request $request, Response $response, $args) use ($conn) {

    $data = $request->getParsedBody();
    // Validation des champs
    if (empty($data['Nom']) || empty($data['Prenom']) || empty($data['Email']) || empty($data['NumPermis'])) {
        $response->getBody()->write(json_encode(['error' => 'Champs obligatoires manquants.']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    try {
        // --- ÉTAPE A : Vérifier si l'email OU le permis existe déjà ---
        $checkSql = "SELECT idClient FROM Client WHERE Email = :email OR NumPermis = :numPermis LIMIT 1";
        $stmt = $conn->prepare($checkSql);
        $stmt->execute([
            ':email' => $data['Email'],
            ':numPermis' => $data['NumPermis']
        ]);
        $existingClient = $stmt->fetch();

        if ($existingClient) {
            // --- ÉTAPE B : UPDATE (Le client existe via Email ou Permis) ---
            $idClient = $existingClient['idClient'];

            // Note : On met aussi à jour l'Email et le Permis au cas où l'un des deux aurait changé 
            // par rapport à l'autre identifiant trouvé.
            $sql = "UPDATE Client SET 
                        Nom = :nom, 
                        Prenom = :prenom, 
                        Email = :email,
                        NumTel = :numTel, 
                        NumPermis = :numPermis, 
                        DateNaiss = :dateNaiss, 
                        Nationalite = :nationalite 
                    WHERE idClient = :id";

            $params = [
                ':nom' => [$data['Nom'], \PDO::PARAM_STR],
                ':prenom' => [$data['Prenom'], \PDO::PARAM_STR],
                ':email' => [$data['Email'], \PDO::PARAM_STR],
                ':numTel' => [$data['NumTel'] ?? null, \PDO::PARAM_STR],
                ':numPermis' => [$data['NumPermis'], \PDO::PARAM_STR],
                ':dateNaiss' => [$data['DateNaiss'] ?? null, \PDO::PARAM_STR],
                ':nationalite' => [$data['Nationalite'] ?? null, \PDO::PARAM_STR],
                ':id' => [$idClient, \PDO::PARAM_INT]
            ];

            $conn->executeQuery($sql, $params);
            $message = 'Client existant (Email ou Permis reconnu) mis à jour avec succès';
            $status = 200;

        } else {
            // --- ÉTAPE C : INSERT (Aucune correspondance trouvée) ---
            $sql = "INSERT INTO Client (Nom, Prenom, Email, NumTel, NumPermis, DateNaiss, Nationalite) 
                    VALUES (:nom, :prenom, :email, :numTel, :numPermis, :dateNaiss, :nationalite)";

            $params = [
                ':nom' => [$data['Nom'], \PDO::PARAM_STR],
                ':prenom' => [$data['Prenom'], \PDO::PARAM_STR],
                ':email' => [$data['Email'], \PDO::PARAM_STR],
                ':numTel' => [$data['NumTel'] ?? null, \PDO::PARAM_STR],
                ':numPermis' => [$data['NumPermis'], \PDO::PARAM_STR],
                ':dateNaiss' => [$data['DateNaiss'] ?? null, \PDO::PARAM_STR],
                ':nationalite' => [$data['Nationalite'] ?? null, \PDO::PARAM_STR]
            ];

            $conn->executeQuery($sql, $params);
            $idClient = $conn->getLastInsertId();
            $message = 'Nouveau client créé avec succès';
            $status = 201;
        }

        $response->getBody()->write(json_encode([
            'message' => $message,
            'idClient' => $idClient
        ]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);

    } catch (\Exception $e) {
        $response->getBody()->write(json_encode(['error' => 'Erreur technique : ' . $e->getMessage()]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// -----------------------------------------------------------------------
// SECTION : CONTRATS
// -----------------------------------------------------------------------

// GET : Liste des contrats
$app->get('/contrat', function (Request $request, Response $response, $args) use ($conn) {
    $conn->executeQuery("SELECT * FROM Contrat");
    $client = $conn->getResults();

    $response->getBody()->write(json_encode($client));
    return $response->withHeader('Content-Type', 'application/json');
});


// POST : Ajouter un contrat
$app->post('/modif/contrat', function (Request $request, Response $response, $args) use ($conn) {

    $data = $request->getParsedBody();

    if (empty($data['DateDebut']) || empty($data['DateFin'])) {
        $response->getBody()->write(json_encode([
            'error' => 'Les champs DateDebut et DateFin sont obligatoires.'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $sql = "INSERT INTO Contrat (DateDebut, DateFin, Statut, IdClient, EtatAvant, EtatApres, IdVehicule, Marque, NomModele, AnneeModele) 
            VALUES (:dateDebut, :dateFin, :statut, :idClient, :etatAvant, :etatApres, :idVehicule, :marque, :nomModele, :anneeModele)";

    $params = [
        ':dateDebut' => [$data['DateDebut'], \PDO::PARAM_STR],
        ':dateFin' => [$data['DateFin'], \PDO::PARAM_STR],
        ':statut' => [$data['Statut'] ?? 'EnCoursValidation', \PDO::PARAM_STR],
        ':idClient' => [$data['IdClient'] ?? null, \PDO::PARAM_INT],
        ':etatAvant' => [$data['EtatAvant'] ?? null, \PDO::PARAM_INT],
        ':etatApres' => [$data['EtatApres'] ?? null, \PDO::PARAM_INT],
        ':idVehicule' => [$data['IdVehicule'] ?? null, \PDO::PARAM_STR],
        ':marque' => [$data['Marque'] ?? null, \PDO::PARAM_STR],
        ':nomModele' => [$data['NomModele'] ?? null, \PDO::PARAM_STR],
        ':anneeModele' => [$data['AnneeModele'] ?? null, \PDO::PARAM_STR]
    ];

    try {
        $conn->executeQuery($sql, $params);

        $response->getBody()->write(json_encode([
            'message' => 'Contrat créé avec succès'
        ]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);

    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([
            'error' => 'Erreur lors de la création du contrat : ' . $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// DELETE : Supprimer un contrat
$app->delete('/contrat/{idContrat}', function (Request $request, Response $response, array $args) use ($conn) {

    // 1️⃣ Récupération et validation de l'ID
    $id = (int) $args['idContrat'];

    if ($id <= 0) {
        $response->getBody()->write(json_encode([
            'error' => 'ID invalide'
        ]));
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }

    // 2️⃣ Vérifier si l'utilisateur existe
    $conn->executeQuery(
        "SELECT idContrat FROM Contrat WHERE idContrat = :idContrat",
        [':idContrat' => [$id, \PDO::PARAM_INT]]
    );

    $user = $conn->getResults();

    if (empty($user)) {
        $response->getBody()->write(json_encode([
            'error' => 'Contrat non trouvé'
        ]));
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(404);
    }

    // 3️⃣ Suppression
    $conn->executeQuery(
        "DELETE FROM Contrat WHERE idContrat = :idContrat",
        [':idContrat' => [$id, \PDO::PARAM_INT]]
    );

    // 4️⃣ Réponse OK
    $response->getBody()->write(json_encode([
        'message' => 'Contrat supprimé avec succès'
    ]));

    return $response->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

// PATCH : Mettre à jour la date de fin d'un contrat
$app->patch('/contrat/{id}', function (Request $request, Response $response, $args) use ($conn) {
    $id = $args['id'];
    $data = $request->getParsedBody();

    if (empty($data['DateFin'])) {
        $response->getBody()->write(json_encode([
            'error' => 'Le champ DateFin est obligatoire.'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $checkSql = "SELECT Statut FROM Contrat WHERE idContrat = :id";
    $checkParams = ['id' => $id];

    try {
        $stmt = $conn->prepare($checkSql);

        $stmt->execute($checkParams);

        $checkResult = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$checkResult) {
            $response->getBody()->write(json_encode(['error' => 'Contrat non trouvé.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        if ($checkResult['Statut'] === 'Terminé') {
            $response->getBody()->write(json_encode(['error' => 'Impossible de modifier la date de fin d\'un contrat terminé.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        $sql = "UPDATE Contrat SET DateFin = :dateFin WHERE idContrat = :id";

        $params = [
            'dateFin' => $data['DateFin'],
            'id' => $id
        ];

        $updateStmt = $conn->prepare($sql);
        $updateStmt->execute($params);

        $response->getBody()->write(json_encode([
            'message' => 'Date de fin du contrat mise à jour avec succès'
        ]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([
            'error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// -----------------------------------------------------------------------
// SECTION : UTILISATEURS (users)
// -----------------------------------------------------------------------

// GET : Liste des utilisateurs par role (optionnel)
$app->get('/users', function (Request $request, Response $response, $args) use ($conn) {

    $conn->executeQuery("SELECT * FROM users");
    
    $users = $conn->getResults();

    $response->getBody()->write(json_encode($users));
    return $response->withHeader('Content-Type', 'application/json');
});

// DELETE : Supprimer un utilisateur
$app->delete('/users/{id}', function (Request $request, Response $response, array $args) use ($conn) {

    // 1️⃣ Récupération et validation de l'ID
    $id = (int) $args['id'];

    if ($id <= 0) {
        $response->getBody()->write(json_encode([
            'error' => 'ID invalide'
        ]));
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }

    // 2️⃣ Vérifier si l'utilisateur existe
    $conn->executeQuery(
        "SELECT id FROM users WHERE id = :id",
        [':id' => [$id, \PDO::PARAM_INT]]
    );

    $user = $conn->getResults();

    if (empty($user)) {
        $response->getBody()->write(json_encode([
            'error' => 'Utilisateur non trouvé'
        ]));
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(404);
    }

    // 3️⃣ Suppression
    $conn->executeQuery(
        "DELETE FROM users WHERE id = :id",
        [':id' => [$id, \PDO::PARAM_INT]]
    );

    // 4️⃣ Réponse OK
    $response->getBody()->write(json_encode([
        'message' => 'Utilisateur supprimé avec succès'
    ]));

    return $response->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

// POST : Ajouter un utilisateur
$app->post('/add/users', function (Request $request, Response $response) use ($conn) {

    $data = $request->getParsedBody();

    $username = $data['username'] ?? null;
    $password = $data['password'] ?? null;
    $role = $data['role'] ?? 'client';

    if (!$username || !$password) {
        $response->getBody()->write(json_encode([
            'error' => 'username et password sont obligatoires'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $conn->executeQuery(
            "INSERT INTO users (username, password, role)
             VALUES (:username, :password, :role)",
            [
                ':username' => [$username, \PDO::PARAM_STR],
                ':password' => [$hashedPassword, \PDO::PARAM_STR],
                ':role' => [$role, \PDO::PARAM_STR]
            ]
        );

        $response->getBody()->write(json_encode([
            'message' => 'Utilisateur créé'
        ]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);

    } catch (\PDOException $e) {

        if ($e->getCode() === '23000') {
            $response->getBody()->write(json_encode([
                'error' => 'Username déjà existant'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
        }

        $response->getBody()->write(json_encode([
            'error' => 'Erreur serveur'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// PUT /users/{id} - Modification d'un utilisateur
$app->put('/users/{id}', function (Request $request, Response $response, $args) use ($conn) {

    $userId = (int) $args['id'];
    $data = $request->getParsedBody();

    if (empty($data)) {
        $response->getBody()->write(json_encode([
            'error' => 'Aucune donnée envoyée'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $fields = [];
    $params = [':id' => [$userId, \PDO::PARAM_INT]];

    // --- Username ---
    if (!empty($data['username'])) {
        $fields[] = 'username = :username';
        $params[':username'] = [$data['username'], \PDO::PARAM_STR];
    }

    // --- Role ---
    if (!empty($data['role'])) {
        $fields[] = 'role = :role';
        $params[':role'] = [$data['role'], \PDO::PARAM_STR];
    }

    // --- Mot de passe ---
    if (!empty($data['password'])) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $fields[] = 'password = :password';
        $params[':password'] = [$hashedPassword, \PDO::PARAM_STR];
    }

    if (empty($fields)) {
        $response->getBody()->write(json_encode([
            'error' => 'Aucun champ valide à mettre à jour'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";

    try {
        $conn->executeQuery($sql, $params);

        $response->getBody()->write(json_encode([
            'message' => 'Utilisateur mis à jour avec succès'
        ]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

    } catch (\PDOException $e) {

        if ($e->getCode() === '23000') {
            $response->getBody()->write(json_encode([
                'error' => 'Username déjà existant'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
        }

        $response->getBody()->write(json_encode([
            'error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()
        ]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// -----------------------------------------------------------------------
// SECTION : STATISTIQUES
// -----------------------------------------------------------------------

// GET : Contrats dont la date de début ou de fin est dans le mois à venir
$app->get('/stats/contrats-prochains', function (Request $request, Response $response, $args) use ($conn) {

    $query = "
        SELECT 
            c.DateDebut,
            c.DateFin,
            v.Marque,
            v.Nom AS Modele
        FROM Contrat c
        JOIN Vehicule v ON c.IdVehicule = v.NumSerie
        WHERE (c.DateDebut BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 MONTH))
           OR (c.DateFin   BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 MONTH))
        ORDER BY c.DateDebut ASC
    ";

    try {
        $conn->executeQuery($query);
        $results = $conn->getResults();

        $response->getBody()->write(json_encode([
            'contrats_prochains' => $results
        ]));

        return $response->withHeader('Content-Type', 'application/json');

    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([
            'error' => 'Erreur lors de la récupération des contrats : ' . $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});

// GET : Nombre total d'utilisateurs
$app->get('/stats/total-users', function (Request $request, Response $response, $args) use ($conn) {

    $query = "SELECT COUNT(*) AS totalUsers FROM users";

    try {
        $conn->executeQuery($query);
        $result = $conn->getResults();

        $total = isset($result[0]['totalUsers']) ? (int) $result[0]['totalUsers'] : 0;

        $response->getBody()->write(json_encode([
            'total_users' => $total
        ]));

        return $response->withHeader('Content-Type', 'application/json');

    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([
            'error' => 'Erreur lors du comptage des utilisateurs : ' . $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});

// GET : Voiture la plus louée
$app->get('/stats/voiture-plus-louee', function (Request $request, Response $response, $args) use ($conn) {

    $query = "
        SELECT 
            v.Marque,
            v.Nom AS Modele,
            v.ImagePath,
            COUNT(*) AS nb_locations
        FROM Contrat c
        JOIN Vehicule v ON c.IdVehicule = v.NumSerie
        WHERE c.Statut = 'Terminé'
        GROUP BY v.Marque, v.Nom
        ORDER BY nb_locations DESC
        LIMIT 1
    ";

    try {
        $conn->executeQuery($query);
        $rows = $conn->getResults();

        if (empty($rows)) {
            $response->getBody()->write(json_encode([
                'voiture_plus_louee' => null
            ]));
        } else {
            $response->getBody()->write(json_encode([
                'voiture_plus_louee' => $rows[0]
            ]));
        }

        return $response->withHeader('Content-Type', 'application/json');

    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([
            'error' => 'Erreur lors de la récupération de la voiture la plus louée : ' . $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});

// GET : Planning mensuel des contrats actifs
$app->get('/stats/planning-mensuel', function (Request $request, Response $response, $args) use ($conn) {

    $query = "
        SELECT 
            c.DateDebut,
            c.DateFin,
            v.Marque,
            v.Nom
        FROM Contrat c
        JOIN Vehicule v ON v.NumSerie = c.IdVehicule
        WHERE 
            c.DateFin >= CURDATE()
            AND c.DateDebut <= LAST_DAY(CURDATE())
        ORDER BY c.DateDebut ASC
    ";

    try {
        $conn->executeQuery($query);
        $results = $conn->getResults();

        $response->getBody()->write(json_encode([
            'planning_mensuel' => $results
        ]));

        return $response->withHeader('Content-Type', 'application/json');

    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([
            'error' => 'Erreur lors de la récupération du planning : ' . $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});

// GET : Revenu mensuel
$app->get('/stats/revenus-mensuel', function (Request $request, Response $response) use ($conn) {

    $query = "
        SELECT SUM(m.Prix) AS total
        FROM Contrat c
        JOIN Vehicule v ON c.IdVehicule = v.NumSerie
        JOIN Modele m 
            ON m.Marque = v.Marque
           AND m.Nom = v.Nom
           AND m.Annee = v.Annee
        WHERE MONTH(c.DateDebut) = MONTH(CURRENT_DATE())
          AND YEAR(c.DateDebut) = YEAR(CURRENT_DATE())
    ";

    try {
        $conn->executeQuery($query);
        $results = $conn->getResults();


        $total = 0.0;
        if (!empty($results) && $results[0]['total'] !== null) {
            $total = (float) $results[0]['total'];
        }

        $response->getBody()->write(json_encode([
            'monthlyIncome' => $total

        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([

            'error' => 'Erreur lors du calcul du revenu mensuel'

        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});

// GET : Véhicules avec contrôle technique bientôt expiré
$app->get('/stats/controle-technique-bientot-expire', function (Request $request, Response $response, $args) use ($conn) {

    $query = "
        SELECT 
            v.Marque,
            v.Nom AS Modele,
            v.DateExpirationControleTech
        FROM Vehicule v
        WHERE v.DateExpirationControleTech <= DATE_ADD(CURDATE(), INTERVAL 2 MONTH)
        ORDER BY v.DateExpirationControleTech ASC
    ";

    try {
        $conn->executeQuery($query);
        $results = $conn->getResults();

        $response->getBody()->write(json_encode([
            'vehicules_controle_technique_bientot_expire' => $results
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([
            'error' => 'Erreur lors de la récupération des contrôles techniques : ' . $e->getMessage()
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});


// -----------------------------------------------------------------------
// SECTION : AUTHENTIFICATION
// -----------------------------------------------------------------------

// POST : Authentification utilisateur
$app->post('/login', function (Request $request, Response $response, $args) use ($conn) {
    $data = $request->getParsedBody();
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($username) || empty($password)) {
        $response->getBody()->write(json_encode(['error' => 'Identifiant et mot de passe requis']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $conn->executeQuery(
        "SELECT * FROM users WHERE username = :username",
        [':username' => [$username, \PDO::PARAM_STR]]
    );

    $users = $conn->getResults();

    if (empty($users)) {
        $response->getBody()->write(json_encode(['success' => false, 'message' => 'Utilisateur inconnu']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }

    $user = $users[0];

    if (password_verify($password, $user['password'])) {

        $payload = [
            "success" => true,
            "token" => bin2hex(random_bytes(16)),
            "role" => $user['role'],
            "username" => $user['username'],
            "id" => $user['id']
        ];

        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    } else {
        $response->getBody()->write(json_encode(['success' => false, 'message' => 'Mot de passe incorrect']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }
});


// -----------------------------------------------------------------------
// SECTION : RAPPELS
// -----------------------------------------------------------------------

// GET : Liste des rappels personnalisés
$app->get('/rappels', function (Request $request, Response $response, $args) use ($conn) {
    $conn->executeQuery("SELECT * FROM Rappel ORDER BY Date ASC");
    $rappels = $conn->getResults();

    $response->getBody()->write(json_encode($rappels));
    return $response->withHeader('Content-Type', 'application/json');
});

// POST : Ajouter un rappel personnalisé
$app->post('/rappel', function (Request $request, Response $response, $args) use ($conn) {
    $data = $request->getParsedBody();

    if (empty($data['Titre']) || empty($data['Description']) || empty($data['Date'])) {
        $response->getBody()->write(json_encode([
            'error' => 'Les champs Titre, Description et Date sont obligatoires.'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $sql = "INSERT INTO Rappel (Titre, Description, Date) VALUES (:titre, :description, :date)";
    $params = [
        ':titre' => [$data['Titre'], \PDO::PARAM_STR],
        ':description' => [$data['Description'], \PDO::PARAM_STR],
        ':date' => [$data['Date'], \PDO::PARAM_STR]
    ];

    try {
        $conn->executeQuery($sql, $params);
        $response->getBody()->write(json_encode([
            'message' => 'Rappel ajouté avec succès'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([
            'error' => 'Erreur lors de l\'ajout du rappel : ' . $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});


$app->put('/contrat/{id}', function (Request $request, Response $response, $args) use ($conn) {
    $id = (int) $args['id'];
    $data = $request->getParsedBody();

    if (empty($data['DateDebut']) || empty($data['DateFin']) || empty($data['Vehicule']) || empty($data['Client'])) {
        $response->getBody()->write(json_encode(['error' => 'Champs obligatoires manquants']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $sql = "UPDATE Contrat SET DateDebut = :dateDebut, DateFin = :dateFin, IdVehicule = :vehicule, IdClient = :client WHERE idContrat = :id";
    $params = [
        ':dateDebut' => [$data['DateDebut'], \PDO::PARAM_STR],
        ':dateFin' => [$data['DateFin'], \PDO::PARAM_STR],
        ':vehicule' => [$data['Vehicule'], \PDO::PARAM_STR],
        ':client' => [$data['Client'], \PDO::PARAM_INT],
        ':id' => [$id, \PDO::PARAM_INT]
    ];

    try {
        $conn->executeQuery($sql, $params);
        $response->getBody()->write(json_encode(['message' => 'Contrat mis à jour avec succès']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    } catch (\Exception $e) {
        $response->getBody()->write(json_encode(['error' => 'Erreur : ' . $e->getMessage()]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

$app->patch('/contrat/{id}/statut', function (Request $request, Response $response, $args) use ($conn) {
    $id = $args['id'];
    $data = $request->getParsedBody();

    if (empty($data['Statut'])) {
        $response->getBody()->write(json_encode(['error' => 'Le champ Statut est obligatoire.']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $sql = "UPDATE Contrat SET Statut = :statut WHERE idContrat = :id";
    $params = [
        'statut' => $data['Statut'],
        'id' => $id
    ];

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        $response->getBody()->write(json_encode([
            'message' => 'Statut du contrat mis à jour avec succès'
        ]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([
            'error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});



// -----------------------------------------------------------------------
// LANCEMENT DE L'APPLICATION
// -----------------------------------------------------------------------

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setErrorHandler(
    Slim\Exception\HttpNotFoundException::class,
    function ($request, $exception, $displayErrorDetails) use ($app) {
        $response = $app->getResponseFactory()->createResponse();
        $response->getBody()->write(json_encode([
            "error" => "Route non dispo ou inexistante"
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }
);

$app->run();
