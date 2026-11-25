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

## 🚀 Lancer le projet (1 seule commande)

Prérequis : soit **Docker seul** (recommandé, rien d'autre à installer), soit PHP 8.x + Docker (base MySQL) et, pour l'admin Blazor, le SDK .NET 9. Sans le SDK .NET 9, `start.ps1` / `start.sh` lancent tout sauf Blazor (utiliser Docker pour Blazor).

| Système | Commande |
|---|---|
| Windows | `.\start.ps1` (ou double-clic sur `start.bat`) |
| macOS / Linux | `./start.sh` |
| Tout dans Docker (sans PHP/.NET installés) | `docker compose up --build` |

Services lancés : base MySQL (port 3307, schéma + données de `BaseDeDonnees/rentpark.sql` importés automatiquement), API (`http://localhost:8880`), site (`http://localhost:9990`), Blazor (`http://localhost:5019`). Ctrl+C arrête tout ; les logs sont dans `logs/`.

Comptes de test (valables sur le site et sur Blazor) :

| Compte | Mot de passe | Rôle |
|---|---|---|
| `admin` | `admin123` | admin : tableau de bord, utilisateurs, clients, flotte, réservations, planning |
| `employe` | `employe123` | employé : flotte, réservations, planning |
| `client` | `client123` | client : réservation, paramètres |

L'inscription publique crée toujours un compte client. Un employé ou un admin est créé depuis la page Utilisateurs par un admin.

Options : `-NoBlazor`, `-NoDb` (utiliser une base déjà disponible, ex. tunnel SSH du dessus).

### Configuration (variables d'environnement)

| Variable | Défaut | Rôle |
|---|---|---|
| `DB_HOST` / `DB_PORT` / `DB_NAME` / `DB_USER` / `DB_PASS` | `127.0.0.1` / `3307` / `dbaltixier1` / `altixier1` / `achanger` | Base de données |
| `API_BASE_URL` | `http://localhost:8880/` | API appelée par le site (en déploiement : URL du pod) |
| `API_URL` / `IMAGES_URL` | `http://localhost:8880` / `http://localhost:9990` | API et images vues par Blazor |
| `SMTP_PASS` | vide | Mot de passe d'application Gmail pour les alertes mail |

Pour repartir d'une base vierge avec Docker : `docker compose down -v` puis `docker compose up --build`.

Base distante de l'IUT : `ssh -L 3307:londres.uca.local:3306 <user>@ssh.iut-clermont.uca.fr`, puis `.\start.ps1 -NoDb`.

### Devis

Après le formulaire de réservation, le site affiche un devis (HT, TVA 20 %, TTC) imprimable. Le bouton « Imprimer le devis » ouvre l'impression du navigateur, d'où l'on peut enregistrer en PDF. Il n'y a pas de génération PDF côté serveur.

### Dépannage

- Page Blazor sans style : rafraîchissement forcé (Ctrl+F5), le navigateur a chargé la page pendant le démarrage.
- Après un redémarrage de Blazor, se reconnecter.
- MySQL sensible à la casse (Linux hors Docker) : les requêtes du site utilisent la table `users`, la base fournie la nomme `Users`. Lancer MySQL avec `--lower-case-table-names=1` (déjà fait dans `docker-compose.yml`).
- Tests PHP : `cd siteSAE2A && vendor/bin/phpunit` (7 tests échouent encore, antérieurs à la mise en place du lancement unique).

## 💻 Blazor

Documentation complète dans [docsBlazor](https://github.com/NeyorDEV/RentPark/tree/main/docsBlazor). Lancé automatiquement par `start.ps1` / `start.sh` (SDK .NET 9 requis) ou par `docker compose up --build` (http://localhost:5019), ou depuis Visual Studio / Rider (`Blazor/BlazorAdminRentPark/BlazorAdminRentPark.slnx`).

## 📱 Application Android

`start.ps1` / `start.sh` écoutent déjà sur `0.0.0.0` (API 8880, images 9990) : l'émulateur Android les atteint via `10.0.2.2`. Dans `RentParkKotlin`, l'URL de l'API (`RetrofitInstance.kt`) pointe encore vers le serveur codefirst : à changer en `http://10.0.2.2:8880/` pour utiliser l'API locale.

Code source : [RentParkKotlin](https://github.com/NeyorDEV/RentPark/tree/main/RentParkKotlin)

## ✉️ Configuration du mail (SMTP)

Le mot de passe d'application Gmail n'est plus dans le code. Fournis-le de l'une de ces deux façons :

- variable d'environnement `SMTP_PASS` ;
- ou fichier `siteSAE2A/config/config.local.php` (copie `config.local.example.php`, il est ignoré par git).
