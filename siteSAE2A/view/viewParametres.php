<?php
if (!isset($role)) {
    if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
    $role = $_SESSION['role'] ?? 'guest';
}
$isAdmin = ($role === 'admin');
$settingsFile = __DIR__ . '/../config/settings.json';
$emailNotifEnabled = true;
if (file_exists($settingsFile)) {
    $s = json_decode(file_get_contents($settingsFile), true);
    $emailNotifEnabled = !empty($s['email_notifications']);
}
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
        <li><a href="/siteSAE2A/home"><img src="/siteSAE2A/html/icons/acceuil.jpg" alt="Accueil"> Accueil</a></li>
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
                    <input id="emailNotif" type="checkbox" <?= $emailNotifEnabled ? 'checked' : ''; ?>>
                    <span class="slider"></span>
                </label>
                <div id="notifMessage" role="status" aria-live="polite" style="display:none;margin-top:10px;padding:8px 12px;border-radius:6px;background:#e6fff0;color:#0b3;box-shadow:0 2px 6px rgba(0,0,0,0.08);">Paramètre enregistré</div>
            </div>
        </div>
    </div>
</div>
</main>
<script>
(function () {
    const root = document.documentElement;
    const lightBtn = document.querySelector('.light-theme-btn');
    const darkBtn = document.querySelector('.dark-theme-btn');

    function applyTheme(theme) {
        if (theme === 'dark') {
            root.classList.add('dark-theme');
            if (darkBtn) darkBtn.setAttribute('aria-pressed', 'true');
            if (lightBtn) lightBtn.setAttribute('aria-pressed', 'false');
        } else {
            root.classList.remove('dark-theme');
            if (darkBtn) darkBtn.setAttribute('aria-pressed', 'false');
            if (lightBtn) lightBtn.setAttribute('aria-pressed', 'true');
        }
        localStorage.setItem('theme', theme);
    }

    function initTheme() {
        const saved = localStorage.getItem('theme') || 'light';
        applyTheme(saved);
    }
    if (lightBtn) lightBtn.addEventListener('click', () => applyTheme('light'));
    if (darkBtn) darkBtn.addEventListener('click', () => applyTheme('dark'));

</body>
</html>