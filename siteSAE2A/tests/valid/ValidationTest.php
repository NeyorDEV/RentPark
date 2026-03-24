<?php

declare(strict_types=1);

use config\Validation;
use PHPUnit\Framework\TestCase;

final class ValidationTest extends TestCase
{
    public function testValVoitureWithValidInputsSanitizesAndKeepsInteger(): void
    {
        $modele = '<script>208</script>';
        $couleur = '  rouge  ';
        $puissance = '110';
        $errors = [];

        Validation::val_voiture($modele, $couleur, $puissance, $errors);

        $this->assertSame([], $errors);
        $this->assertSame('&lt;script&gt;208&lt;/script&gt;', $modele);
        $this->assertSame('  rouge  ', $couleur);
        $this->assertSame(110, $puissance);
    }

    public function testValVoitureWithMissingFieldsAndInvalidPowerAddsErrors(): void
    {
        $modele = '';
        $couleur = '';
        $puissance = 'abc';
        $errors = [];

        Validation::val_voiture($modele, $couleur, $puissance, $errors);

        $this->assertContains('Le modèle est obligatoire', $errors);
        $this->assertContains('La couleur est obligatoire', $errors);
        $this->assertContains('La puissance doit être un entier', $errors);
        $this->assertSame(0, $puissance);
    }

    public function testValUserWithEmptyFieldsAddsRequiredError(): void
    {
        $username = ' ';
        $password = '';
        $role = 'client';
        $errors = [];

        Validation::val_user($username, $password, $role, $errors);

        $this->assertContains('Tous les champs sont requis.', $errors);
    }

    public function testValUserWithInvalidRoleAddsRoleErrorAndSanitizesUsername(): void
    {
        $username = '<b>alice</b>';
        $password = 'secret';
        $role = 'manager';
        $errors = [];

        Validation::val_user($username, $password, $role, $errors);

        $this->assertContains('Rôle invalide.', $errors);
        $this->assertSame('&lt;b&gt;alice&lt;/b&gt;', $username);
    }

    public function testValConnectionWithWrongPasswordAddsError(): void
    {
        $username = 'alice';
        $password = 'wrong-password';
        $savepass = password_hash('good-password', PASSWORD_DEFAULT);
        $errors = [];

        Validation::val_connection($username, $password, $savepass, $errors);

        $this->assertContains("Mot de passe ou nom d'utilisateur invalide.", $errors);
    }

    public function testValConnectionWithGoodPasswordDoesNotAddErrorAndSanitizesUsername(): void
    {
        $username = '<admin>';
        $password = 'good-password';
        $savepass = password_hash('good-password', PASSWORD_DEFAULT);
        $errors = [];

        Validation::val_connection($username, $password, $savepass, $errors);

        $this->assertSame([], $errors);
        $this->assertSame('&lt;admin&gt;', $username);
    }
}
