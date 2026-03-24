<?php

declare(strict_types=1);

use modele\Connection;
use PHPUnit\Framework\TestCase;

final class ConnectionTest extends TestCase
{
    protected function setUp(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite extension is required for ConnectionTest.');
        }
    }

    public function testExecuteQueryAndGetResultsWithParameters(): void
    {
        $connection = new Connection('sqlite::memory:', '', '');

        $this->assertTrue($connection->executeQuery('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT)'));
        $this->assertTrue($connection->executeQuery(
            'INSERT INTO users (name) VALUES (:name)',
            [':name' => ['alice', PDO::PARAM_STR]]
        ));
        $this->assertTrue($connection->executeQuery(
            'SELECT id, name FROM users WHERE name = :name',
            [':name' => ['alice', PDO::PARAM_STR]]
        ));

        $rows = $connection->getResults();

        $this->assertCount(1, $rows);
        $this->assertSame('alice', $rows[0]['name']);
    }

    public function testGetResultsReturnsEmptyArrayWhenNoRowMatches(): void
    {
        $connection = new Connection('sqlite::memory:', '', '');

        $connection->executeQuery('CREATE TABLE cars (id INTEGER PRIMARY KEY, modele TEXT)');
        $connection->executeQuery('SELECT * FROM cars WHERE modele = :modele', [':modele' => ['BMW', PDO::PARAM_STR]]);

        $this->assertSame([], $connection->getResults());
    }

    public function testExecuteQueryThrowsOnInvalidSql(): void
    {
        $connection = new Connection('sqlite::memory:', '', '');

        $this->expectException(PDOException::class);

        $connection->executeQuery('SELECT * FROM table_that_does_not_exist');
    }
}
