<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use modeleApi\Connection;

// Autoload PSR-4
$loader = require_once __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('BL\\', __DIR__);

// Connexion à la DB
$databaseFactory = require_once __DIR__ . '/../config/database.php';
$conn = $databaseFactory();

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

// ----------------------
// Helpers DRY
// ----------------------
function jsonResponse(Response $response, $data, int $status = 200): Response {
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
}

function getAll(Response $response, Connection $conn, string $table): Response {
    $conn->executeQuery("SELECT * FROM $table");
    return jsonResponse($response, $conn->getResults());
}

function deleteById(Response $response, Connection $conn, string $table, string $idColumn, $id, string $fkErrorMessage = null): Response {
    $conn->executeQuery("SELECT $idColumn FROM $table WHERE $idColumn = :id", [':id' => [$id, \PDO::PARAM_STR]]);
    $item = $conn->getResults();

    if (empty($item)) {
        return jsonResponse($response, ['error' => "$table non trouvé"], 404);
    }

    try {
        $conn->executeQuery("DELETE FROM $table WHERE $idColumn = :id", [':id' => [$id, \PDO::PARAM_STR]]);
    } catch (\PDOException $e) {
        if ($e->getCode() === '23000' && $fkErrorMessage) {
            return jsonResponse($response, ['error' => $fkErrorMessage], 409);
        }
        return jsonResponse($response, ['error' => 'Erreur SQL', 'message' => $e->getMessage()], 500);
    }

    return jsonResponse($response, ['message' => "$table supprimé avec succès"]);
}

// ----------------------
// Routes GET
// ----------------------
$app->get('/voitures', fn($req, $res) => getAll($res, $conn, 'Vehicule'));
$app->get('/vehicules', fn($req, $res) => getAll($res, $conn, 'Vehicule'));
$app->get('/client', fn($req, $res) => getAll($res, $conn, 'Client'));
$app->get('/contrat', fn($req, $res) => getAll($res, $conn, 'Contrat'));
$app->get('/users', fn($req, $res) => getAll($res, $conn, 'users'));

// ----------------------
// Routes DELETE
// ----------------------
$app->delete('/voitures/{numSerie}', fn($req, $res, $args) =>
    deleteById($res, $conn, 'Vehicule', 'NumSerie', $args['numSerie'], 'Cette voiture est utilisée dans un ou plusieurs contrats')
);

$app->delete('/contrat/{id}', fn($req, $res, $args) => deleteById($res, $conn, 'Contrat', 'idContrat', $args['id']));
$app->delete('/users/{id}', fn($req, $res, $args) => deleteById($res, $conn, 'users', 'id', $args['id']));

// ----------------------
// Routes POST
// ----------------------
$app->post('/contrat', function(Request $request, Response $response) use ($conn) {
    $data = $request->getParsedBody();
    if (empty($data['DateDebut']) || empty($data['DateFin'])) {
        return jsonResponse($response, ['error' => 'DateDebut et DateFin obligatoires'], 400);
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
        return jsonResponse($response, ['message' => 'Contrat créé avec succès'], 201);
    } catch (\Exception $e) {
        return jsonResponse($response, ['error' => 'Erreur création contrat', 'message' => $e->getMessage()], 500);
    }
});

$app->post('/users', function(Request $request, Response $response) use ($conn) {
    $data = $request->getParsedBody();
    $nom = $data['nom'] ?? null;
    $mdp = $data['mdp'] ?? null;
    $confirm = $data['confirm'] ?? null;
    $role = $data['role'] ?? 'client';

    if (!$nom || !$mdp || !$role || !$confirm || $mdp !== $confirm) {
        $msg = $mdp !== $confirm ? 'Mots de passe différents' : 'Données manquantes';
        return jsonResponse($response, ['error' => $msg], 400);
    }

    try {
        $conn->executeQuery(
            "INSERT INTO users (username, password, role) VALUES (:nom, :mdp, :role)",
            [
                ':nom'  => [$nom, \PDO::PARAM_STR],
                ':mdp'  => [password_hash($mdp, PASSWORD_DEFAULT), \PDO::PARAM_STR],
                ':role' => [$role, \PDO::PARAM_STR]
            ]
        );
        return jsonResponse($response, ['message' => 'Utilisateur créé !'], 201);
    } catch (\Exception $e) {
        return jsonResponse($response, ['error' => 'Erreur BDD : ' . $e->getMessage()], 500);
    }
});

// ----------------------
// PATCH /contrat/{id} - mise à jour DateFin
// ----------------------
$app->patch('/contrat/{id}', function(Request $request, Response $response, $args) use ($conn) {
    $id = $args['id'];
    $data = $request->getParsedBody();

    if (empty($data['DateFin'])) {
        return jsonResponse($response, ['error' => 'DateFin obligatoire'], 400);
    }

    $stmt = $conn->prepare("SELECT Statut FROM Contrat WHERE idContrat = :id");
    $stmt->execute(['id' => $id]);
    $contrat = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$contrat) return jsonResponse($response, ['error' => 'Contrat non trouvé'], 404);
    if ($contrat['Statut'] === 'Terminé') return jsonResponse($response, ['error' => 'Impossible de modifier un contrat terminé'], 403);

    $updateStmt = $conn->prepare("UPDATE Contrat SET DateFin = :dateFin WHERE idContrat = :id");
    $updateStmt->execute(['dateFin' => $data['DateFin'], 'id' => $id]);

    return jsonResponse($response, ['message' => 'DateFin mise à jour']);
});

// ----------------------
// Middleware / Erreurs
// ----------------------
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setErrorHandler(
    Slim\Exception\HttpNotFoundException::class,
    fn($req, $ex, $display) => jsonResponse($app->getResponseFactory()->createResponse(), ['error' => 'Route non dispo'], 404)
);

$app->run();
