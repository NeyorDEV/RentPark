<?php

declare(strict_types=1);

use modele\Voiture;
use PHPUnit\Framework\TestCase;

final class VoitureTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $voiture = new Voiture(3, 'BMW', 'noir', '180');

        $this->assertSame(3, $voiture->getVoiture());
        $this->assertSame('BMW', $voiture->getModele());
        $this->assertSame('noir', $voiture->getCouleur());
        $this->assertSame('180', $voiture->getPuissance());
    }

    public function testToStringContainsMainFields(): void
    {
        $voiture = new Voiture(4, 'Clio', 'blanc', '90');
        $render = (string)$voiture;

        $this->assertStringContainsString('Voiture: 4', $render);
        $this->assertStringContainsString('Modèle: Clio', $render);
        $this->assertStringContainsString('Couleur: blanc', $render);
        $this->assertStringContainsString('Puissance:90', $render);
    }
}
