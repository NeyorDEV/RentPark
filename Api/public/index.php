<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use modeleApi\Connection;

// Autoload PSR-4
$loader = require_once __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('BL\\', __DIR__);

// Récupération de la connexion via database.php
$databaseFactory = require_once __DIR__ . '/../config/database.php';
$conn = $databaseFactory(); // $conn est maintenant une instance de Connection

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

// -----------------------------------------------------------------------
//  /voitures - GET et DELETE
// -----------------------------------------------------------------------

// GET
$app->get('/voitures', function (Request $request, Response $response, $args) use ($conn) {
    $conn->executeQuery("SELECT * FROM Vehicule");
    $vehicules = $conn->getResults();

    $response->getBody()->write(json_encode($vehicules));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/vehicules', function (Request $request, Response $response, $args) use ($conn) {
    $conn->executeQuery("SELECT * FROM Vehicule");
    $vehicules = $conn->getResults();

    $response->getBody()->write(json_encode($vehicules));
    return $response->withHeader('Content-Type', 'application/json');
});

// DELETE (numSerie)
$app->delete('/voitures/{numSerie}', function ($request, $response, $args) use ($conn) {
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

// GET voiture par NumSerie
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

// -----------------------------------------------------------------------
//  /client - GET et POST
// -----------------------------------------------------------------------

$app->get('/client', function (Request $request, Response $response, $args) use ($conn) {
    $conn->executeQuery("SELECT * FROM Client");
    $client = $conn->getResults();

    $response->getBody()->write(json_encode($client));
    return $response->withHeader('Content-Type', 'application/json');
});

// POST 
$app->post('/client', function (Request $request, Response $response, $args) use ($conn) {
    
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
        ':nom'         => [$data['Nom'], \PDO::PARAM_STR],
        ':prenom'      => [$data['Prenom'], \PDO::PARAM_STR],
        ':email'       => [$data['Email'], \PDO::PARAM_STR],
        ':numTel'      => [$data['NumTel'] ?? null, \PDO::PARAM_STR],
        ':numPermis'   => [$data['NumPermis'], \PDO::PARAM_STR],
        ':dateNaiss'   => [$data['DateNaiss'] ?? null, \PDO::PARAM_STR],
        ':nationalite' => [$data['Nationalite'] ?? null, \PDO::PARAM_STR]
    ];

    try {
        $conn->executeQuery($sql, $params);
        
        // On récupère l'ID du client qui vient d'être créé pour pouvoir l'utiliser pour le contrat
        $idClient = $conn->getLastInsertId(); 

        $response->getBody()->write(json_encode([
            'message'  => 'Client créé avec succès',
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

// -----------------------------------------------------------------------
//  /contrat - GET, POST et DELETE
// -----------------------------------------------------------------------

// GET
$app->get('/contrat', function (Request $request, Response $response, $args) use ($conn) {
    $conn->executeQuery("SELECT * FROM Contrat");
    $client = $conn->getResults();

    $response->getBody()->write(json_encode($client));
    return $response->withHeader('Content-Type', 'application/json');
});

// POST 
$app->post('/contrat', function (Request $request, Response $response, $args) use ($conn) {
    
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

// DELETE 
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

// -----------------------------------------------------------------------
//  /users - GET, DELETE et POST
// -----------------------------------------------------------------------

// GET
$app->get('/users', function (Request $request, Response $response, $args) use ($conn) {
    $conn->executeQuery("SELECT * FROM users");
    $users = $conn->getResults();

    $response->getBody()->write(json_encode($users));
    return $response->withHeader('Content-Type', 'application/json');
});

// DELETE
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

// POST : Ajout d'un utilisateur
$app->post('/users', function (Request $request, Response $response) use ($conn) {
    // 1️⃣ Récupération des données
    $data = $request->getParsedBody();

    $nom     = $data['nom']     ?? null;
    $mdp     = $data['mdp']     ?? null;
    $confirm = $data['confirm'] ?? null;
    $role    = $data['role']    ?? 'client';

    // 2️⃣ Validation (Champs vides ou mots de passe différents)
    if (!$nom || !$mdp || !$role || !$confirm || $confirm !== $mdp) {
        $msg = ($confirm !== $mdp) ? 'Mots de passe différents' : 'Données manquantes';
        $response->getBody()->write(json_encode(['error' => $msg]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    // 3️⃣ Hachage et Insertion
    $hashedPassword = password_hash($mdp, PASSWORD_DEFAULT);

    try {
        $conn->executeQuery(
            "INSERT INTO users (username, password, role) VALUES (:nom, :mdp, :role)",
            [
                ':nom'  => [$nom, \PDO::PARAM_STR],
                ':mdp'  => [$hashedPassword, \PDO::PARAM_STR],
                ':role' => [$role, \PDO::PARAM_STR]
            ]
        );

        $response->getBody()->write(json_encode(['message' => 'Utilisateur créé !']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);

    } catch (\Exception $e) {
        $response->getBody()->write(json_encode(['error' => 'Erreur BDD : ' . $e->getMessage()]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// -------------------------------------------------------------------------------------------------

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
