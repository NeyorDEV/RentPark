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

// -----------------------------------------------------------------------
//  /client - GET
// -----------------------------------------------------------------------

$app->get('/client', function (Request $request, Response $response, $args) use ($conn) {
    $conn->executeQuery("SELECT * FROM Client");
    $client = $conn->getResults();

    $response->getBody()->write(json_encode($client));
    return $response->withHeader('Content-Type', 'application/json');
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
