<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentPark - Planning</title>
    
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
    <link rel="stylesheet" href="/siteSAE2A/html/css/planning.css">
</head>
<body>

<nav>
    <ul class="menu">
        <li><a href="/siteSAE2A/home"><i class="fa-solid fa-house"></i> Accueil</a></li>
        <?php if ($role === 'admin') : ?>       
            <li><a href="/siteSAE2A/dashboard"><i class="fa-solid fa-chart-line"></i> Tableau de bord</a></li>
        <?php endif; ?>
        <li><a href="/siteSAE2A/voitures"><i class="fa-solid fa-car"></i> Flotte Automobile</a></li>
        <li><a href="/siteSAE2A/planning" class="active"><i class="fa-solid fa-file-contract"></i> Planning</a></li>
        <li><a href="/siteSAE2A/reservation"><i class="fa-solid fa-calendar-check"></i> Contrats</a></li>
        <li><a href="/siteSAE2A/utilisateurs"><i class="fa-solid fa-users-gear"></i> Utilisateurs</a></li>
        <li><a href="/siteSAE2A/parametres"><i class="fa-solid fa-gears"></i> Paramètres</a></li>
        <li class="logout-item"><a href="/siteSAE2A/deconnection"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a></li>
    </ul>
</nav>

<div class="main-content">
    <div class="header-container">
        <h1>Planning Mensuel</h1>
        <div class="month-navigation">
    <a href="?month=<?= $results['prevMonth'] ?>&year=<?= $results['prevYear'] ?>">
        ← Mois précédent
    </a>

    <strong><?= htmlspecialchars($results['currentMonthLabel']) ?></strong>

    <a href="?month=<?= $results['nextMonth'] ?>&year=<?= $results['nextYear'] ?>">
        Mois suivant →
    </a>
</div>

    </div>

    <div class="planning-card">
        <div class="calendar">
            <?php foreach ($results['calendar'] as $day): ?>
                <div class="day <?= $day['isToday'] ? 'today' : '' ?>">
                    <div class="date"><?= htmlspecialchars($day['label']) ?></div>

                    <div class="events">
                        <?php if (!empty($day['events'])): ?>
                            <?php foreach ($day['events'] as $event): ?>
                                <?php if ($event['type'] === 'depart'): ?>
                                    <span class="event-letter depart" title="<?= htmlspecialchars($event['label']) ?>">D</span>
                                <?php else: ?>
                                    <span class="event-letter retour" title="<?= htmlspecialchars($event['label']) ?>">R</span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

</body>
</html>