<?php

declare(strict_types=1);

use modele\Reservation;
use PHPUnit\Framework\TestCase;

final class ReservationTest extends TestCase
{
    public function testConstructorStoresAllValues(): void
    {
        $dateDebut = new DateTime('2026-03-20 10:00:00');
        $dateFin = new DateTime('2026-03-22 18:30:00');

        $reservation = new Reservation(
            42,
            $dateDebut,
            $dateFin,
            'VF1AAAAA123456789',
            7,
            99
        );

        $this->assertSame(42, $reservation->getIdContrat());
        $this->assertSame($dateDebut, $reservation->getDateDebut());
        $this->assertSame($dateFin, $reservation->getDateFin());
        $this->assertSame('VF1AAAAA123456789', $reservation->getVehiculeVin());
        $this->assertSame(7, $reservation->getClientId());
        $this->assertSame(99, $reservation->getEtatDesLieuxId());
    }

    public function testConstructorRejectsInvalidClientType(): void
    {
        $this->expectException(TypeError::class);

        new Reservation(
            1,
            new DateTime('2026-03-01'),
            new DateTime('2026-03-02'),
            'VIN123',
            'not-an-int',
            4
        );
    }
}
