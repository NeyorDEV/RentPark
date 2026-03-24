<?php

use PHPUnit\Framework\TestCase;
use controleur\UserController;
use modele\UserGateway;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use modele\Connection;

class UserControllerTest extends TestCase
{
    private $controller;
    private $userGatewayMock;
    private $mockHandler;

    protected function setUp(): void
    {
        // 1. Nettoyer les superglobales avant chaque test
        $_GET = [];
        $_POST = [];
        $_SESSION = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // 2. Créer un mock (bouchon) partiel du contrôleur pour éviter d'exécuter afficherVue()
        // et SURTOUT désactiver le constructeur original qui contient un exit(0);
        $this->controller = $this->getMockBuilder(UserController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['afficherVue']) // On mock afficherVue pour qu'elle ne fasse rien
            ->getMock();

        // 3. Créer des mocks pour les dépendances
        $this->userGatewayMock = $this->createMock(UserGateway::class);
        
        // 4. Préparer le mock pour Guzzle (API)
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $apiClientMock = new Client(['handler' => $handlerStack]);

        // 5. Utiliser la Réflexion pour injecter nos mocks dans les propriétés privées du contrôleur
        $reflection = new \ReflectionClass($this->controller);
        
        $userGatewayProp = $reflection->getProperty('userGateway');
        $userGatewayProp->setAccessible(true);
        $userGatewayProp->setValue($this->controller, $this->userGatewayMock);

        $apiClientProp = $reflection->getProperty('apiClient');
        $apiClientProp->setAccessible(true);
        $apiClientProp->setValue($this->controller, $apiClientMock);
    }

    public function testCarsFiltreParPrixEtEnergie()
    {
        // Préparer les filtres GET
        $_GET['energie'] = 'Electrique';
        $_GET['prix_max'] = '50';

        // Simuler la réponse de l'API Guzzle avec deux véhicules
        $voituresApi = [
            ['NumSerie' => '1', 'Nom' => 'Zoe', 'Energie' => 'Electrique', 'Prix' => 45],
            ['NumSerie' => '2', 'Nom' => 'Clio', 'Energie' => 'Essence', 'Prix' => 40],
            ['NumSerie' => '3', 'Nom' => 'Tesla', 'Energie' => 'Electrique', 'Prix' => 100], // Trop cher
        ];
        
        $this->mockHandler->append(
            new Response(200, [], json_encode($voituresApi))
        );

        // On s'attend à ce que afficherVue soit appelée une fois avec la Zoe uniquement (index 0)
        $this->controller->expects($this->once())
            ->method('afficherVue')
            ->with(
                $this->equalTo('cars'),
                $this->isType('array'),
                $this->callback(function($results) {
                    return count($results) === 1 && $results[0]['Nom'] === 'Zoe';
                })
            );

        $dVueErreur = [];
        $this->controller->cars($dVueErreur);
    }

    public function testConnectionSuccesInitialiseSession()
    {
        $_POST['username'] = 'john_doe';
        $_POST['password'] = 'password123';

        // Simuler le comportement de la BDD via le UserGateway
        $this->userGatewayMock->method('getHashPass')->willReturn(password_hash('password123', PASSWORD_DEFAULT));
        $this->userGatewayMock->method('getRole')->willReturn('client');
        $this->userGatewayMock->method('getClientId')->willReturn(42);

        $dVueErreur = [];
        
        // Exécuter la méthode
        $this->controller->connection($dVueErreur);

        // Vérifier que la session a bien été hydratée
        $this->assertEquals('john_doe', $_SESSION['username']);
        $this->assertEquals(42, $_SESSION['idClient']);
        $this->assertEquals('client', $_SESSION['role']);
        $this->assertEmpty($dVueErreur);
    }

    public function testFinaliserReservationEchoueSiMineur()
    {
        // Un utilisateur né en 2015 (mineur)
        $_POST['datenaiss'] = '2015-01-01';
        $_POST['date_debut'] = '2030-01-01';
        $_POST['date_fin'] = '2030-01-05';
        
        $dVueErreur = [];

        // On s'attend à ce qu'une erreur soit levée et envoyée à la vue 'erreur'
        $this->controller->expects($this->once())
            ->method('afficherVue')
            ->with(
                $this->equalTo('erreur'),
                $this->callback(function($erreurs) {
                    return strpos($erreurs[0], '18 ans') !== false;
                })
            );

        $this->controller->finaliserReservation($dVueErreur);
    }

    public function testFinaliserReservationSucces()
    {
        $_POST['datenaiss'] = '1990-01-01'; // Majeur
        // Dates valides dans le futur
        $dateDebut = (new \DateTime('+1 day'))->format('Y-m-d');
        $dateFin = (new \DateTime('+3 days'))->format('Y-m-d');
        
        $_POST['date_debut'] = $dateDebut;
        $_POST['date_fin'] = $dateFin;
        $_POST['num_serie'] = 'VIN123';

        // 1. Simuler la réponse pour la récupération du véhicule
        $this->mockHandler->append(
            new Response(200, [], json_encode(['Marque' => 'Peugeot', 'Nom' => '208', 'Annee' => 2020]))
        );

        // 2. Simuler la réponse de la création du client (API POST /client)
        $this->mockHandler->append(
            new Response(200, [], json_encode(['idClient' => 99]))
        );

        // 3. Simuler la réponse de la création du contrat (API POST /modif/contrat)
        $this->mockHandler->append(
            new Response(200, [], json_encode(['message' => 'Contrat créé avec succès']))
        );

        $dVueErreur = [];

        // On s'attend à ce que la vue 'confirmationSucces' soit appelée
        $this->controller->expects($this->once())
            ->method('afficherVue')
            ->with($this->equalTo('confirmationSucces'), $this->isType('array'));

        $this->controller->finaliserReservation($dVueErreur);
    }
}