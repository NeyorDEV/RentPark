<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning - RentPark</title>
    <link rel="stylesheet" href="/siteSAE2A/html/css/menu.css">
    <link rel="stylesheet" href="/siteSAE2A/html/css/planning.css">
    <link rel="shortcut icon" href="/siteSAE2A/html/icons/favicon.ico" type="image/x-icon">
</head>

<nav>
    <ul class="menu">
        <li><a href="/siteSAE2A/home"><img src="html/icons/acceuil.jpg" alt="Accueil"> Accueil</a></li>
        <li><a href="/siteSAE2A/dashboard"><img src="html/icons/dashboard.png" alt="Tableau de bord"> Tableau de bord</a></li>
        <li><a href="/sitesae2A/voitures"><img src="html/icons/voiture.png" alt="Flotte"> Flotte Automobile</a></li>
        <li><a href="/siteSAE2A/planning"><img src="html/icons/contrat.png" alt="Contrats"> Contrats</a></li>
        <li><a href="/siteSAE2A/reservation"><img src="html/icons/reservation.png" alt="Réservation"> Réservation</a></li>
        <li><a href="siteSAE2A/../utilisateurs"><img src="html/icons/user.png" alt="Utilisateurs"> Utilisateur</a></li>
        <li><a href="/siteSAE2A/parametres"><img src="html/icons/settings.png" alt="Paramètres"> Paramètres</a></li>
        <li><a href="/siteSAE2A/deconnection"><img src="html/icons/logout.png" alt="se déconnecter"> déconnexion</a></li>
    </ul>
</nav>
<div class="planning">
    <h2>Planning du mois</h2>

    <div class="calendar">
        <?php foreach ($results['calendar'] as $day): ?>
            <div class="day <?= $day['isToday'] ? 'today' : '' ?>">
                <div class="date"><?= htmlspecialchars($day['label']) ?></div>

                <div class="events">
                    <?php foreach ($day['events'] as $event): ?>
                        <span class="event <?= $event['type'] ?>"
                              title="<?= htmlspecialchars($event['label']) ?>">
                            <?= $event['type'] === 'depart' ? '🚗' : '🔁' ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

