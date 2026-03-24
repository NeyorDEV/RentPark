<?php
namespace tests;

use PHPUnit\Framework\TestCase;
use controleur\EmployeControleur;
use modele\VehicleGateway;
use modele\UserGateway;
use modele\ReservationGateway;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class EmployeControleurTest extends TestCase
{
    private $employeControleur;
    private $vehicleGatewayMock;
    private $userGatewayMock;
    private $reservationGatewayMock;
    private $apiClientMock;

    protected function setUp(): void
    {
        // 1. Création des Mocks pour nos dépendances
        $this->vehicleGatewayMock = $this->createMock(VehicleGateway::class);
        $this->userGatewayMock = $this->createMock(UserGateway::class);
        $this->reservationGatewayMock = $this->createMock(ReservationGateway::class);

        // CORRECTION ICI : On utilise directement createMock, beaucoup plus propre et sans erreur IDE
        $this->apiClientMock = $this->createMock(Client::class);

        // 2. Création d'un mock partiel du Contrôleur
        $this->employeControleur = $this->getMockBuilder(EmployeControleur::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['afficherVue', 'checkEmploye'])
            ->getMock();

        // 3. Injection des mocks dans les propriétés privées
        $this->setPrivateProperty('gateway', $this->vehicleGatewayMock);
        $this->setPrivateProperty('userGateway', $this->userGatewayMock);
        $this->setPrivateProperty('reservationGateway', $this->reservationGatewayMock);
        $this->setPrivateProperty('apiClient', $this->apiClientMock);

        // Réinitialisation des superglobales
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [];
        $_POST = [];
    }

    /**
     * Utilitaire pour injecter dans les propriétés privées (car pas d'Injection de Dépendances dans le constructeur)
     */
    private function setPrivateProperty(string $propertyName, $value): void
    {
        $reflection = new \ReflectionClass(EmployeControleur::class);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($this->employeControleur, $value);
    }

    /**
     * Utilitaire pour appeler des méthodes privées
     */
    private function callPrivateMethod(string $methodName, array $args = [])
    {
        $reflection = new \ReflectionClass(EmployeControleur::class);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($this->employeControleur, $args);
    }

    // --- TESTS DES MÉTHODES PUBLIQUES ---

    public function testListeVoituresGetSuccess()
    {
        $mockBody = json_encode([['id' => 1, 'modele' => 'Peugeot 208']]);
        $responseMock = new Response(200, [], $mockBody);

        // On simule un appel réussi à l'API Guzzle
        $this->apiClientMock->expects($this->once())
            ->method('get')
            ->with('voitures')
            ->willReturn($responseMock);

        // On vérifie que la vue "flotte" est bien appelée avec les bonnes données
        $this->employeControleur->expects($this->once())
            ->method('afficherVue')
            ->with('flotte', [], [['id' => 1, 'modele' => 'Peugeot 208']]);

        $this->employeControleur->listeVoitures([]);
    }

    public function testListeVoituresGetApiError()
    {
        $request = new Request('GET', 'voitures');
        
        // On simule une erreur de l'API (ex: 500 ou timeout)
        $this->apiClientMock->expects($this->once())
            ->method('get')
            ->willThrowException(new RequestException('Error', $request));

        // On s'attend à ce que l'erreur soit ajoutée au tableau d'erreurs
        $this->employeControleur->expects($this->once())
            ->method('afficherVue')
            ->with('flotte', ["Impossible de récupérer les véhicules depuis l’API."], []);

        $this->employeControleur->listeVoitures([]);
    }

    public function testAffichePlanningCalendrierValide()
    {
        $_GET['month'] = 10;
        $_GET['year'] = 2023;

        $mockContracts = [
            [
                'idContrat' => 1,
                'Marque' => 'Renault',
                'Nom' => 'Clio',
                'Client' => 'Jean Dupont',
                'DateDebut' => '2023-10-05',
                'DateFin' => '2023-10-10',
                'Statut' => 'EnCoursValidation'
            ]
        ];

        // On simule le retour de la base de données
        $this->reservationGatewayMock->expects($this->once())
            ->method('getMonthlyPlanning')
            ->willReturn($mockContracts);

        // On vérifie les calculs de dates envoyés à la vue
        $this->employeControleur->expects($this->once())
            ->method('afficherVue')
            ->with('planning', [], $this->callback(function ($results) {
                // Vérifications
                return $results['currentMonthLabel'] === 'Octobre 2023'
                    && $results['prevMonth'] === 9
                    && $results['nextMonth'] === 11
                    && isset($results['calendar']['2023-10-05']['events'][0]) // Event de départ
                    && isset($results['calendar']['2023-10-10']['events'][0]); // Event de retour
            }));

        $this->employeControleur->affichePlanning([]);
    }

    // --- TESTS DES MÉTHODES PRIVÉES (Via la Réflexion) ---

    public function testRechercherUtilisateurAvecMotCle()
    {
        $_GET['q'] = 'Alice';
        $mockUsers = [['id' => 2, 'username' => 'Alice']];

        $this->userGatewayMock->expects($this->once())
            ->method('rechercherUtilisateur')
            ->with('Alice')
            ->willReturn($mockUsers);

        $this->employeControleur->expects($this->once())
            ->method('afficherVue')
            ->with('user', [], $mockUsers);

        $dVueErreur = [];
        $this->callPrivateMethod('rechercherUtilisateur', [&$dVueErreur]);
    }

    public function testRechercherReservationFiltrePassees()
    {
        $_GET['filtre'] = 'passees'; // On veut tester le filtre des réservations
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        $mockContracts = json_encode([
            ['idContrat' => 1, 'DateDebut' => '2020-01-01', 'DateFin' => $yesterday, 'Statut' => 'Terminé'], // Devrait être gardé
            ['idContrat' => 2, 'DateDebut' => '2099-01-01', 'DateFin' => '2099-12-31', 'Statut' => 'Futur'] // Devrait être ignoré
        ]);

        $responseMock = new Response(200, [], $mockContracts);
        
        $this->apiClientMock->expects($this->once())
            ->method('get')
            ->with('contrat')
            ->willReturn($responseMock);

        // La vue doit recevoir UNIQUEMENT le contrat 1
        $this->employeControleur->expects($this->once())
            ->method('afficherVue')
            ->with('reservation', [], $this->callback(function ($results) {
                return count($results) === 1 && $results[0]['idContrat'] === 1;
            }));

        $dVueErreur = [];
        $this->callPrivateMethod('rechercherReservation', [&$dVueErreur]);
    }
}