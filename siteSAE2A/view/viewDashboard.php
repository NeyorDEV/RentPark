<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>RentPark - Dashboard</title>
    <script>
    (function () {
        try {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-theme');
            } else {
                document.documentElement.classList.remove('dark-theme');
            }
        } catch (e) { }
    })();
    </script>
    <link rel="stylesheet" href="/siteSAE2A/html/css/parametres.css">
    <link rel="stylesheet" href="html/css/dashboard.css">
    <link rel="icon" type="image/png" href="html/icons/voiture.png">
    <link rel="shortcut icon" href="html/icons/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<nav>
    <ul class="menu">
        <li><a href="/siteSAE2A/home"><i class="fa-solid fa-house"></i> Accueil</a></li>
        <li><a href="/siteSAE2A/dashboard" class="active"><i class="fa-solid fa-chart-line"></i> Tableau de bord</a></li>
        <li><a href="/sitesae2A/voitures"><i class="fa-solid fa-car"></i> Flotte Automobile</a></li>
        <li><a href="/siteSAE2A/planning"><i class="fa-solid fa-file-contract"></i> Contrats</a></li>
        <li><a href="/siteSAE2A/reservation"><i class="fa-solid fa-calendar-check"></i> Réservation</a></li>
        <li><a href="/siteSAE2A/utilisateurs"><i class="fa-solid fa-users-gear"></i> Utilisateurs</a></li>
        <li><a href="/siteSAE2A/parametres"><i class="fa-solid fa-gears"></i> Paramètres</a></li>
        <li class="logout-item"><a href="/siteSAE2A/deconnection"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a></li>
    </ul>
</nav>

<div class="main-content">
    <h1>Tableau de bord</h1>

    <div class="dashboard-grid">
        
        <div class="card stat-card">
            <h2>Nombre d'utilisateurs</h2>
            <div class="circle-chart" 
                 id="users-circle" 
                 style="--final-value: <?= min($results["totalUsers"], 100) ?>;">
                <span id="users-counter">0</span>
            </div>
        </div>

        <div class="card stat-card">
            <h2>Voiture la plus louée</h2>
            <?php if (!empty($results["voiturePlusLouee"])) : $bestCar = $results["voiturePlusLouee"]; ?>
                <div class="best-car-container">
                    <div class="info">
                        <strong><?= htmlspecialchars($bestCar["Marque"]) ?></strong>
                        <span><?= htmlspecialchars($bestCar["Modele"]) ?></span>
                        <small>Louée <?= (int)$bestCar["nb_locations"] ?> fois</small>
                    </div>
                    <div class="image-wrapper">
                        <img src="<?= !empty($bestCar["ImagePath"]) ? htmlspecialchars($bestCar["ImagePath"]) : 'html/icons/voiture.png' ?>" alt="Véhicule">
                    </div>
                </div>
            <?php else : ?>
                <p class="empty-data">Aucune donnée disponible</p>
            <?php endif; ?>
        </div>

        <div class="card stat-card">
            <h2>Revenus mensuels</h2>
            <div class="circle-chart" 
                 id="revenus-circle" 
                 style="--final-value: <?= min(($results["revenusMensuels"] / 300000) * 100, 100) ?>;">
                <span id="revenus-counter">0</span>€
            </div>
        </div>

        <div class="card large-card">
            <h2>Rappel</h2>
            <div class="scrollable-list">
                <?php if (!empty($results['alerts'])): ?>
                    <?php foreach ($results['alerts'] as $alert): ?>
                        <div class="list-item">
                            <span class="badge">Alerte</span>
                            <p><?= htmlspecialchars($alert['label']) ?> – <?= htmlspecialchars($alert['vehicule']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="list-item empty">✅ Aucun rappel en cours</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card large-card">
            <h2>Planning</h2>
            <div class="scrollable-list">
                <?php if (!empty($results['planning'])): ?>
                    <?php foreach ($results['planning'] as $event): ?>
                        <div class="list-item">
                            <span class="date-tag"><?= htmlspecialchars(date('d/m/Y', strtotime($event['time']))) ?></span>
                            <p><?= htmlspecialchars($event['action']) ?> : <?= htmlspecialchars($event['vehicule']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="list-item empty">Aucun événement prévu</div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<script>
// Fonction d'animation des compteurs
function animateCounter(id, targetValue, duration) {
    const counter = document.getElementById(id);
    if(!counter) return;
    let start = 0;
    const fps = 60;
    const totalFrames = Math.round(duration / (1000 / fps));
    const increment = targetValue / totalFrames;

    const timer = setInterval(() => {
        start += increment;
        if (start >= targetValue) {
            start = targetValue;
            clearInterval(timer);
        }
        counter.textContent = Math.floor(start).toLocaleString();
    }, 1000 / fps);
}

// Initialisation des animations
document.addEventListener('DOMContentLoaded', () => {
    animateCounter("revenus-counter", <?= (float)$results["revenusMensuels"] ?>, 800);
    animateCounter("users-counter", <?= (int)$results["totalUsers"] ?>, 800);
});
</script>

</body>
</html>