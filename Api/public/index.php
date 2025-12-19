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


$app->get('/vehicules', function (Request $request, Response $response, $args) use ($conn) {
    $conn->executeQuery("SELECT * FROM Vehicule");
    $vehicules = $conn->getResults();

    $response->getBody()->write(json_encode($vehicules));
    return $response->withHeader('Content-Type', 'application/json');
});


$app->get('/client', function (Request $request, Response $response, $args) use ($conn) {
    $conn->executeQuery("SELECT * FROM Client");
    $client = $conn->getResults();

    $response->getBody()->write(json_encode($client));
    return $response->withHeader('Content-Type', 'application/json');
});


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
