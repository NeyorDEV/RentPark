<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use modele\VehicleGateway;
use modele\Connection;

class VehicleGatewayTest extends TestCase
{
    private $connectionMock;
    private VehicleGateway $gateway;

    protected function setUp(): void
    {
        // Création du mock pour la classe Connection
        $this->connectionMock = $this->createMock(Connection::class);
        
        // Injection du mock dans la Gateway
        $this->gateway = new VehicleGateway($this->connectionMock);
    }

    public function testGetAllReturnsArray(): void
    {
        $expectedData = [
            ['voiture' => 1, 'modele' => 'Clio', 'couleur' => 'Rouge'],
            ['voiture' => 2, 'modele' => '208', 'couleur' => 'Bleu']
        ];

        $this->connectionMock->expects($this->once())
            ->method('executeQuery')
            ->with($this->stringContains('SELECT * FROM cars'));
            
        $this->connectionMock->method('getResults')
            ->willReturn($expectedData);

        $result = $this->gateway->getAll();
        $this->assertEquals($expectedData, $result);
        $this->assertIsArray($result);
    }

    public function testAdd(): void
    {
        $expectedParams = [
            ':modele'     => ['Mustang', \PDO::PARAM_STR],
            ':couleur'    => ['Noir', \PDO::PARAM_STR],
            ':puissance'  => ['450ch', \PDO::PARAM_STR],
            ':image_path' => ['/img/mustang.png', \PDO::PARAM_STR],
        ];

        $this->connectionMock->expects($this->once())
            ->method('executeQuery')
            ->with($this->stringContains('INSERT INTO cars'), $expectedParams);

        $this->gateway->add('Mustang', 'Noir', '450ch', '/img/mustang.png');
    }

    public function testAddWithDefaultImagePath(): void
    {
        $expectedParams = [
            ':modele'     => ['Clio', \PDO::PARAM_STR],
            ':couleur'    => ['Blanc', \PDO::PARAM_STR],
            ':puissance'  => ['90ch', \PDO::PARAM_STR],
            ':image_path' => ['', \PDO::PARAM_STR], // Vérification de la valeur par défaut
        ];

        $this->connectionMock->expects($this->once())
            ->method('executeQuery')
            ->with($this->stringContains('INSERT INTO cars'), $expectedParams);

        $this->gateway->add('Clio', 'Blanc', '90ch');
    }

    public function testDelete(): void
    {
        $expectedParams = [
            ':id' => [5, \PDO::PARAM_INT]
        ];

        $this->connectionMock->expects($this->once())
            ->method('executeQuery')
            ->with($this->stringContains('DELETE FROM cars'), $expectedParams);

        $this->gateway->delete(5);
    }

    public function testUpdate(): void
    {
        $expectedParams = [
            ':modele'   => ['Golf', \PDO::PARAM_STR],
            ':couleur'  => ['Gris', \PDO::PARAM_STR],
            ':puissance'=> ['150ch', \PDO::PARAM_STR],
            ':id'       => [10, \PDO::PARAM_INT],
        ];

        $this->connectionMock->expects($this->once())
            ->method('executeQuery')
            ->with($this->stringContains('UPDATE cars SET modele = :modele'), $expectedParams);

        $this->gateway->update(10, 'Golf', 'Gris', '150ch');
    }

    public function testRechercherVoitures(): void
    {
        $motCle = 'Peugeot';
        $expectedParams = [
            ':q' => ["%$motCle%", \PDO::PARAM_STR]
        ];

        $expectedData = [['voiture' => 3, 'modele' => 'Peugeot 308']];

        $this->connectionMock->expects($this->once())
            ->method('executeQuery')
            ->with($this->stringContains('SELECT * FROM cars WHERE modele LIKE :q'), $expectedParams);
            
        $this->connectionMock->method('getResults')
            ->willReturn($expectedData);

        $result = $this->gateway->rechercherVoitures($motCle);
        $this->assertEquals($expectedData, $result);
    }

    public function testGetMostRentedCarReturnsFirstRow(): void
    {
        $expectedDbResult = [
            ['Marque' => 'Renault', 'Modele' => 'Clio', 'ImagePath' => '/img/clio.jpg', 'nb_locations' => 12],
            ['Marque' => 'Peugeot', 'Modele' => '208', 'ImagePath' => '/img/208.jpg', 'nb_locations' => 8]
        ];

        $this->connectionMock->expects($this->once())
            ->method('executeQuery')
            ->with($this->stringContains('SELECT'));
            
        $this->connectionMock->method('getResults')
            ->willReturn($expectedDbResult);

        $result = $this->gateway->getMostRentedCar();
        
        // On vérifie que seule la première ligne est retournée
        $this->assertEquals($expectedDbResult[0], $result);
        $this->assertIsArray($result);
    }

    public function testGetMostRentedCarReturnsNullIfEmpty(): void
    {
        $this->connectionMock->expects($this->once())
            ->method('executeQuery');
            
        $this->connectionMock->method('getResults')
            ->willReturn([]);

        $result = $this->gateway->getMostRentedCar();
        $this->assertNull($result);
    }

    public function testGetRentableVehiculesBetweenDates(): void
    {
        $dateDepart = '2024-05-01';
        $dateRetour = '2024-05-10';

        $expectedParams = [
            ':date_depart' => [$dateDepart, \PDO::PARAM_STR],
            ':date_retour' => [$dateRetour, \PDO::PARAM_STR]
        ];

        $expectedData = [['NumSerie' => 'ABC1234', 'Marque' => 'Toyota']];

        $this->connectionMock->expects($this->once())
            ->method('executeQuery')
            ->with($this->stringContains('SELECT * FROM Vehicule'), $expectedParams);
            
        $this->connectionMock->method('getResults')
            ->willReturn($expectedData);

        $result = $this->gateway->getRentableVehiculesBetweenDates($dateDepart, $dateRetour);
        $this->assertEquals($expectedData, $result);
    }

    public function testGetVehiculesControleTechniqueBientotExpire(): void
    {
        $expectedData = [
            ['Marque' => 'Citroen', 'Modele' => 'C3', 'DateExpirationControleTech' => '2024-06-15']
        ];

        $this->connectionMock->expects($this->once())
            ->method('executeQuery')
            ->with($this->stringContains('DATE_ADD(CURDATE(), INTERVAL 2 MONTH)'));
            
        $this->connectionMock->method('getResults')
            ->willReturn($expectedData);

        $result = $this->gateway->getVehiculesControleTechniqueBientotExpire();
        $this->assertEquals($expectedData, $result);
    }
}