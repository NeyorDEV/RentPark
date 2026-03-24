<?php

declare(strict_types=1);

use modele\User;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function testConstructorStoresValues(): void
    {
        $user = new User(15, 'alice', 'hashed-pass', 'admin');

        $this->assertSame(15, $user->getId());
        $this->assertSame('alice', $user->getUsername());
        $this->assertSame('hashed-pass', $user->getPassword());
        $this->assertSame('admin', $user->getRole());
    }

    public function testUserCanHaveNullId(): void
    {
        $user = new User(null, 'bob', 'secret', 'client');

        $this->assertNull($user->getId());
        $this->assertSame('client', $user->getRole());
    }
}
