<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use controleur\AdminControleur;
use modele\VehicleGateway;
use modele\UserGateway;
use modele\ReservationGateway;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use ReflectionClass;

class AdminControleurTest extends TestCase
{
    private $controleur;
    private $vehicleGatewayMock;
    private $userGatewayMock;
    private $reservationGatewayMock;
    private $apiClientMock;

    protected function setUp(): void
    {
        // 1. Création des Mocks pour les dépendances
        $this->vehicleGatewayMock = $this->createMock(VehicleGateway::class);
        $this->userGatewayMock = $this->createMock(UserGateway::class);
        $this->reservationGatewayMock = $this->createMock(ReservationGateway::class);
        $this->apiClientMock = $this->createMock(Client::class);

        // 2. Instanciation du contrôleur SANS appeler le constructeur (pour éviter le exit(0))
        $reflection = new ReflectionClass(AdminControleur::class);
        $this->controleur = $reflection->newInstanceWithoutConstructor();

        // 3. Injection des mocks dans les propriétés privées via la réflexion
        $this->injectPrivateProperty($this->controleur, 'gateway', $this->vehicleGatewayMock);
        $this->injectPrivateProperty($this->controleur, 'userGateway', $this->userGatewayMock);
        $this->injectPrivateProperty($this->controleur, 'reservationGateway', $this->reservationGatewayMock);
        $this->injectPrivateProperty($this->controleur, 'apiClient', $this->apiClientMock);

        // Réinitialisation des superglobales pour chaque test
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_POST = [];
        $_GET = [];
    }

    /**
     * Méthode utilitaire pour injecter dans une propriété privée
     */
    private function injectPrivateProperty($object, $propertyName, $value)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    /**
     * Méthode utilitaire pour appeler une méthode privée
     */
    private function callPrivateMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }

    // =========================================================================
    // TESTS DES MÉTHODES
    // =========================================================================

    public function testAfficheDashboardGet()
    {
        // Préparation du comportement des Mocks
        $this->reservationGatewayMock->method('getMonthlyIncome')->willReturn(1500.50);
        $this->vehicleGatewayMock->method('getMostRentedCar')->willReturn(['Marque' => 'Peugeot', 'Modele' => '208']);
        $this->userGatewayMock->method('countUser')->willReturn(42);
        
        $this->vehicleGatewayMock->method('getVehiculesControleTechniqueBientotExpire')
             ->willReturn([['Marque' => 'Renault', 'Modele' => 'Clio']]);

        $this->reservationGatewayMock->method('getContractsForNextMonth')
             ->willReturn([]);

        // Mock de l'API Guzzle pour les rappels
        $jsonResponse = json_encode([
            ['Titre' => 'Vidange', 'Description' => 'Peugeot 208', 'Date' => '2026-04-10']
        ]);
        $responseMock = new Response(200, [], $jsonResponse);
        $this->apiClientMock->method('__call')->with('get', ['rappels'])->willReturn($responseMock);

        // On "mock" la méthode afficherVue car elle tente de faire un include qui va échouer en test
        $controleurMock = $this->getMockBuilder(AdminControleur::class)
                               ->disableOriginalConstructor()
                               ->onlyMethods(['afficherVue'])
                               ->getMock();
        
        // On réinjecte nos gateways dans ce mock spécifique
        $this->injectPrivateProperty($controleurMock, 'reservationGateway', $this->reservationGatewayMock);
        $this->injectPrivateProperty($controleurMock, 'vehicleGateway', $this->vehicleGatewayMock);
        $this->injectPrivateProperty($controleurMock, 'userGateway', $this->userGatewayMock);
        $this->injectPrivateProperty($controleurMock, 'apiClient', $this->apiClientMock);

        // On s'attend à ce que afficherVue soit appelée avec les bonnes données
        $controleurMock->expects($this->once())
                       ->method('afficherVue')
                       ->with('dashboard', [], $this->callback(function($results) {
                           return $results['revenusMensuels'] === 1500.50 
                               && $results['totalUsers'] === 42;
                       }));

        // Exécution
        $controleurMock->afficheDashboard([]);
    }

    public function testRechercherUtilisateurAvecMotCle()
    {
        $_GET['q'] = 'Jean';
        $dVueErreur = [];

        // Comportement attendu du mock
        $this->userGatewayMock->expects($this->once())
             ->method('rechercherUtilisateur')
             ->with('Jean')
             ->willReturn([['id' => 1, 'username' => 'JeanDupont']]);

        // Mock de afficherVue
        $controleurMock = $this->getMockBuilder(AdminControleur::class)
                               ->disableOriginalConstructor()
                               ->onlyMethods(['afficherVue'])
                               ->getMock();
        $this->injectPrivateProperty($controleurMock, 'userGateway', $this->userGatewayMock);

        $controleurMock->expects($this->once())
                       ->method('afficherVue')
                       ->with('user', $dVueErreur, [['id' => 1, 'username' => 'JeanDupont']]);

        // Appel de la méthode privée via la réflexion
        $this->callPrivateMethod($controleurMock, 'rechercherUtilisateur', [$dVueErreur]);
    }

    public function testSupprimerUtilisateurRedirigeSiIdInvalide()
    {
        $_POST['id'] = -1;
        $dVueErreur = [];

        // PHPUnit a du mal avec les appels directs à "header()" et "exit;". 
        // Pour tester le comportement, on capture la sortie ou on vérifie que l'API n'est pas appelée.
        
        $this->apiClientMock->expects($this->never())->method('delete'); // L'API ne doit pas être appelée
        
        // On s'attend à ce que le code produise une erreur fatale ou s'arrête si `exit` n'est pas neutralisé.
        // Remarque : Si vous utilisez vraiment header() et exit(), il est préférable de lancer des exceptions en cas de redirection dans le contrôleur.
        // Pour ce test, on vérifie juste que le mock Guzzle n'est pas déclenché.
        
        // Cette méthode va planter à cause du 'exit' dans ton code d'origine si on l'exécute directement.
        $this->markTestIncomplete('Ce test nécessite la suppression ou le mocking de la fonction exit() native de PHP.');
    }

    public function testAjouterRappel()
    {
        $_POST['Titre'] = 'Test Rappel';
        $_POST['Description'] = 'Description du rappel';
        $_POST['Date'] = '2026-10-10';

        $dVueErreur = [];

        // On vérifie que le client Guzzle fait bien la requête POST
        $this->apiClientMock->expects($this->once())
             ->method('__call') // On utilise __call car les méthodes de Guzzle sont souvent magiques
             ->with('post', [
                 'rappel',
                 ['json' => [
                     'Titre' => 'Test Rappel',
                     'Description' => 'Description du rappel',
                     'Date' => '2026-10-10'
                 ]]
             ]);

        $this->callPrivateMethod($this->controleur, 'ajouterRappel', [&$dVueErreur]);
        $this->assertEmpty($dVueErreur);
    }
}