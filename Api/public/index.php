<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);
//$app->add(\App\Middleware\RequestIdMiddleware::class);

// On charge les routes (attention au chemin)
$routes = require __DIR__ . '/../routes.php';
$routes($app);

$app->run();


