<?php
if (!isset($role)) {
    if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
    $role = $_SESSION['role'] ?? 'guest';
}
$isAdmin = ($role === 'admin');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - RentPark</title>
    <link rel="stylesheet" href="/siteSAE2A/html/css/menu.css">
    <link rel="stylesheet" href="/siteSAE2A/html/css/parametres.css">
    <link rel="shortcut icon" href="/siteSAE2A/html/icons/favicon.ico" type="image/x-icon">
</head>
<body>
<nav>
    <ul class="menu">
        <li><a href="/siteSAE2A/index.php"><img src="/siteSAE2A/html/icons/acceuil.jpg" alt="Accueil"> Accueil</a></li>
        <li><a href="/siteSAE2A/dashboard"><img src="/siteSAE2A/html/icons/dashboard.png" alt="Tableau de bord"> Tableau de bord</a></li>
        <li><a href="/siteSAE2A/voitures"><img src="/siteSAE2A/html/icons/voiture.png" alt="Flotte"> Flotte Automobile</a></li>
        <li><a href="/siteSAE2A/contrats"><img src="/siteSAE2A/html/icons/contrat.png" alt="Contrats"> Contrats</a></li>
        <li><a href="/siteSAE2A/reservation"><img src="/siteSAE2A/html/icons/reservation.png" alt="Réservations"> Réservations</a></li>
        <li><a href="/siteSAE2A/utilisateurs"><img src="/siteSAE2A/html/icons/user.png" alt="Utilisateurs"> Utilisateur</a></li>
        <li><a href="/siteSAE2A/parametres"><img src="/siteSAE2A/html/icons/settings.png" alt="Paramètres"> Paramètres</a></li>
        <li><a href="/siteSAE2A/deconnection"><img src="/siteSAE2A/html/icons/logout.png" alt="se déconnecter"> déconnexion</a></li>
    </ul>
</nav>

<main style="margin-left:22%; padding:24px;">
<div class="parametres-container">
    <div class="parametres-content">
        <h1>Paramètres</h1>

        <div class="parametres-section">
            <h2>Apparence</h2>
            <div class="settings-item">
                <div class="settings-info">
                    <h3>Thème</h3>
                    <p>Choisissez entre thème clair ou sombre</p>
                </div>
                <div class="theme-buttons">
                    <button class="theme-btn light-theme-btn">☀️ Clair</button>
                    <button class="theme-btn dark-theme-btn">🌙 Sombre</button>
                </div>
            </div>
        </div>

        <div class="parametres-section">
            <h2>Notifications</h2>
            <div class="settings-item">
                <div class="settings-info">
                    <h3>Notifications par email</h3>
                    <p>Recevoir les mises à jour par email</p>
                </div>
                <label class="toggle-switch">
                    <input id="emailNotif" type="checkbox">
                    <span class="slider"></span>
                </label>
            </div>
        </div>

    </div>
</div>
</main>

</body>
</html>