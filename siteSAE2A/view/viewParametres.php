<?php
if (!isset($role)) {
    if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
    $role = $role ?? 'guest';
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
    <title>RentPark - Paramètres</title>
    <script>
    (function () {
        try {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.classList.toggle('dark-theme', theme === 'dark');
        } catch (e) { }
    })();
    </script>
    <link rel="icon" type="image/png" href="html/icons/voiture.png">
    <link rel="shortcut icon" href="html/icons/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/siteSAE2A/html/css/commun.css">
    <link rel="stylesheet" href="/siteSAE2A/html/css/parametres.css">
</head>
<body>

<nav>
    <ul class="menu">
        <li><a href="/siteSAE2A/home"><i class="fa-solid fa-house"></i> Accueil</a></li>
        <li><a href="/siteSAE2A/voitures"><i class="fa-solid fa-car"></i> Flotte Automobile</a></li>
        <?php if ($role === 'admin') : ?>
            <li><a href="/siteSAE2A/dashboard"><i class="fa-solid fa-dashboard"></i>Tableau de bord</a></li>
        <?php endif; ?>
        <?php if ($role === 'admin' || $role ==='employe') :?>
            <li><a href="/siteSAE2A/planning"><i class="fa-solid fa-calendar-days "></i> Planning</a></li>
            <li><a href="/siteSAE2A/utilisateurs"><i class="fa-solid fa-user"></i> Utilisateurs</a></li>
        <?php endif;?>
        <?php if ($role !== 'unknown') : ?>
            <li><a href="/siteSAE2A/reservation"><i class="fa-solid fa-file-signature   "></i> Réservations</a></li>
            <li><a href="/siteSAE2A/parametres"><i class="fa-solid fa-gear"></i> Paramètres</a></li>
            <li class="logout-item"><a href="/siteSAE2A/deconnection"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a></li>
        <?php endif;?>
    </ul>
</nav>

<div class="main-content">
    <div class="big-glass-box">
        <h1>Paramètres</h1>

        <div class="section">
            <h3>Mode d'affichage</h3>
            <p>Choisissez entre le mode clair et le mode sombre :</p>
            <div class="theme-buttons-container">
                <button id="btn-light" class="theme-choice-btn">
                    <i class="fa-solid fa-sun"></i> Mode Clair
                </button>
                <button id="btn-dark" class="theme-choice-btn">
                    <i class="fa-solid fa-moon"></i> Mode Sombre
                </button>
            </div>
        </div>

        <hr class="separator">

        <div class="section">
            <h3>Notifications</h3>
            <div class="notif-row">
                <span class="notif-text">Recevoir les alertes de maintenance par email</span>
                <button id="emailNotifBtn" class="toggle-btn <?= $emailNotifEnabled ? 'on' : 'off' ?>">
                    <?= $emailNotifEnabled ? 'Activé' : 'Désactivé' ?>
                </button>
            </div>
            <div id="notifMessage" class="status-hint"></div>
        </div>
    </div>
</div>

<script>
// Changement de thème
document.getElementById('btn-light').addEventListener('click', () => {
    document.documentElement.classList.remove('dark-theme');
    localStorage.setItem('theme', 'light');
});

document.getElementById('btn-dark').addEventListener('click', () => {
    document.documentElement.classList.add('dark-theme');
    localStorage.setItem('theme', 'dark');
});

// Gestion Notifs (Toggle bouton)
document.getElementById('emailNotifBtn').addEventListener('click', async function() {
    const isCurrentlyOn = this.classList.contains('on');
    const newVal = isCurrentlyOn ? 0 : 1;
    
    try {
        const res = await fetch('/siteSAE2A/config/updateSettings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email_notifications: newVal })
        });
        if (res.ok) {
            this.classList.toggle('on');
            this.classList.toggle('off');
            this.textContent = isCurrentlyOn ? 'Désactivé' : 'Activé';
            
            const msg = document.getElementById('notifMessage');
            msg.textContent = "Préférence mise à jour.";
            setTimeout(() => msg.textContent = "", 2500);
        }
    } catch (e) { console.error(e); }
});
</script>

</body>
</html>