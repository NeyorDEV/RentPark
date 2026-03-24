<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use modele\UserGateway;
use modele\Connection;
use modele\User;

class UserGatewayTest extends TestCase
{
    private $connectionMock;
    private UserGateway $gateway;

    protected function setUp(): void
    {
        // On crée un mock de la classe Connection
        $this->connectionMock = $this->createMock(Connection::class);
        
        // On injecte ce mock dans notre Gateway
        $this->gateway = new UserGateway($this->connectionMock);
    }

    public function testLogin(): void
    {
        // Mock de l'entité User
        $userMock = $this->createMock(User::class);
        $userMock->method('getUsername')->willReturn('johndoe');
        $userMock->method('getPassword')->willReturn('hashed_pwd');
        $userMock->method('getRole')->willReturn('ROLE_USER');

        $expectedParams = [
            ':username' => ['johndoe', \PDO::PARAM_STR],
            ':password' => ['hashed_pwd', \PDO::PARAM_STR],
            ':role'     => ['ROLE_USER', \PDO::PARAM_STR],
        ];

        // On s'attend à ce que executeQuery soit appelé une fois avec les bons paramètres
        $this->connectionMock->expects($this->once())
            ->method('executeQuery')
            ->with($this->stringContains('INSERT INTO users'), $expectedParams);

        $this->gateway->login($userMock);
    }

    public function testAddUser(): void
    {
        $expectedParams = [
            ':username' => ['johndoe', \PDO::PARAM_STR],
            ':password' => ['password123', \PDO::PARAM_STR],
            ':role'     => ['ROLE_ADMIN', \PDO::PARAM_STR],
        ];

        $this->connectionMock->expects($this->once())
            ->method('executeQuery')
            ->with($this->stringContains('INSERT INTO users'), $expectedParams);

        $this->gateway->addUser('johndoe', 'password123', 'ROLE_ADMIN');
    }

    public function testGetClientIdReturnsInt(): void
    {
        $this->connectionMock->expects($this->once())
            ->method('executeQuery');
            
        // On simule un retour de la base de données
        $this->connectionMock->method('getResults')
            ->willReturn([['client_id' => 42]]);

        $result = $this->gateway->getClientId('johndoe');
        $this->assertEquals(42, $result);
    }

    public function testGetClientIdReturnsNullIfNotFound(): void
    {
        $this->connectionMock->expects($this->once())
            ->method('executeQuery');
            
        // On simule une base de données qui ne trouve rien
        $this->connectionMock->method('getResults')
            ->willReturn([]);

        $result = $this->gateway->getClientId('unknown_user');
        $this->assertNull($result);
    }

    public function testCountUserReturnsCount(): void
    {
        $this->connectionMock->expects($this->once())
            ->method('executeQuery');
            
        $this->connectionMock->method('getResults')
            ->willReturn([['totalUsers' => 15]]);

        $result = $this->gateway->countUser();
        $this->assertEquals(15, $result);
        $this->assertIsInt($result);
    }

    public function testCountUserReturnsZeroIfEmpty(): void
    {
        $this->connectionMock->expects($this->once())
            ->method('executeQuery');
            
        $this->connectionMock->method('getResults')
            ->willReturn([]);

        $result = $this->gateway->countUser();
        $this->assertEquals(0, $result);
    }

    public function testGetHashPassReturnsHash(): void
    {
        $this->connectionMock->expects($this->once())
            ->method('executeQuery');
            
        $this->connectionMock->method('getResults')
            ->willReturn([['password' => 'my_super_hash']]);

        // Le 2ème paramètre ne sert à rien dans le code actuel, mais on doit le passer
        $result = $this->gateway->getHashPass('johndoe', 'unused_password');
        $this->assertEquals('my_super_hash', $result);
    }

    public function testGetRoleReturnsRoleString(): void
    {
        $this->connectionMock->expects($this->once())
            ->method('executeQuery');
            
        $this->connectionMock->method('getResults')
            ->willReturn([['role' => 'ROLE_ADMIN']]);

        $result = $this->gateway->getRole('admin_user');
        $this->assertEquals('ROLE_ADMIN', $result);
    }

    public function testDeleteUser(): void
    {
        $expectedParams = [
            ':id' => [10, \PDO::PARAM_INT]
        ];

        $this->connectionMock->expects($this->once())
            ->method('executeQuery')
            ->with($this->stringContains('DELETE FROM users'), $expectedParams);

        $this->gateway->deleteUser(10);
    }

    public function testGetAllUserReturnsArray(): void
    {
        $expectedData = [
            ['id' => 1, 'username' => 'alice'],
            ['id' => 2, 'username' => 'bob']
        ];

        $this->connectionMock->expects($this->once())
            ->method('executeQuery')
            ->with($this->stringContains('SELECT * FROM users'));
            
        $this->connectionMock->method('getResults')
            ->willReturn($expectedData);

        $result = $this->gateway->getAllUser();
        $this->assertEquals($expectedData, $result);
        $this->assertIsArray($result);
    }

    public function testRechercherUtilisateur(): void
    {
        $motCle = 'ali';
        $expectedParams = [
            ':q' => ["%$motCle%", \PDO::PARAM_STR]
        ];

        $expectedData = [['id' => 1, 'username' => 'alice']];

        $this->connectionMock->expects($this->once())
            ->method('executeQuery')
            ->with($this->stringContains('SELECT * FROM users WHERE username LIKE'), $expectedParams);
            
        $this->connectionMock->method('getResults')
            ->willReturn($expectedData);

        $result = $this->gateway->rechercherUtilisateur($motCle);
        $this->assertEquals($expectedData, $result);
    }

    public function testUpdate(): void
    {
        $expectedParams = [
            ':username' => ['new_name', \PDO::PARAM_STR],
            ':id'       => [5, \PDO::PARAM_INT],
        ];

        $this->connectionMock->expects($this->once())
            ->method('executeQuery')
            ->with($this->stringContains('UPDATE users SET username'), $expectedParams);

        $this->gateway->update('new_name', 5);
    }
}