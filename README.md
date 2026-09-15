# SAE_2A_RentPark

**Projet scolaire en groupe** - Situation d'Apprentissage et d'Évaluation (SAE 2A)

## 📊 Analyse

- [Analyse](https://github.com/NeyorDEV/RentPark/tree/main/Analyse)

## 🌐 Site Web

- [Web](https://github.com/NeyorDEV/RentPark/tree/main/siteSAE2A)

## 🗄️ Base de Données

MCD, MLD et rapport de BDD dans le dossier BaseDeDonnees

- [BDD](https://github.com/NeyorDEV/RentPark/tree/main/BaseDeDonnees)

## 📐 Conception

- [Conception](https://github.com/NeyorDEV/RentPark/tree/main/Conception)

Nous avons mis une conception initiale de là où nous en sommes dans le projet avec une conception moyenne qui ne respecte pas les principes SOLID mais qui était celle demandée par les professeurs de PHP. La 2e conception est une version améliorée avec un maximum de respect des principes SOLID et l'intégration des webservices qui permettent de mieux respecter ces principes.

## 🔗 Connexion à distance / Besoins pour Blazor

Ouvrez 2 terminaux et faites les deux commandes suivantes en modifiant votre nom d'utilisateur uca.

```bash
ssh -L 8888:londres.uca.local:80 [VotreNomUtilisateur]@ssh.iut-clermont.uca.fr
ssh -L 3307:londres.uca.local:3306 [VotreNomUtilisateur]@ssh.iut-clermont.uca.fr
```

## 🔌 API / Besoins pour Blazor

Pour lancer l'API, placez-vous dans le dépot avec un terminal et faites les commandes suivantes :

```bash
cd Api/public/
php -S localhost:8880
```

## 📸 Serveur pour les images / Besoins pour Blazor

Pour lancer le serveur, placez-vous dans le dépot avec un terminal et faites les commandes suivantes :

```bash
cd siteSAE2A
php -S localhost:9990
```

## 💻 Blazor

1. Assurez-vous d'être sur la branche main.
2. Ouvrez la solution `sae_2a_rentpark/Blazor/BlazorAdminRentPark/BlazorAdminRentPark.slnx`.
3. Lancez le projet avec votre IDE (Visual Studio / Rider).

Documentation complète disponible dans le répertoire [docsBlazor](https://github.com/NeyorDEV/RentPark/tree/main/docsBlazor)

## 📱 Application Android

Pour accéder à l'API, vous ne pouvez pas faire tourner le site en même temps que l'Android en localhost pour le moment.

Solutions : ouvrez un terminal sous :

```bash
php -S 0.0.0.0:8880
```

Le reste est configuré dans Android pour le moment.

Pareil pour le serveur photo, vous devez le lancer en `0.0.0.0:9990`

Code source : [RentParkKotlin](https://github.com/NeyorDEV/RentPark/tree/main/RentParkKotlin)
