<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentPark - Dashboard</title>

    <script>
        (function () {
            try {
                const theme = localStorage.getItem('theme') || 'light';
                document.documentElement.classList.toggle('dark-theme', theme === 'dark');
            } catch (e) { }
        })();
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/siteSAE2A/html/css/commun.css">
    <link rel="stylesheet" href="/siteSAE2A/html/css/dashboard.css">

    <link rel="icon" type="image/png" href="html/icons/voiture.png">
    <link rel="shortcut icon" href="html/icons/favicon.ico" type="image/x-icon">
</head>

<body>

    <nav>
        <ul class="menu">
            <li><a href="/siteSAE2A/home"><i class="fa-solid fa-house"></i> Accueil</a></li>
            <li><a href="/siteSAE2A/dashboard" class="active"><i class="fa-solid fa-chart-line"></i> Tableau de bord</a>
            </li>
            <li><a href="/sitesae2A/voitures"><i class="fa-solid fa-car"></i> Flotte Automobile</a></li>
            <li><a href="/siteSAE2A/planning"><i class="fa-solid fa-file-contract"></i> Planning</a></li>
            <li><a href="/siteSAE2A/reservation"><i class="fa-solid fa-calendar-check"></i> Contrats</a></li>
            <li><a href="/siteSAE2A/utilisateurs"><i class="fa-solid fa-users-gear"></i> Utilisateurs</a></li>
            <li><a href="/siteSAE2A/parametres"><i class="fa-solid fa-gears"></i> Paramètres</a></li>
            <li class="logout-item"><a href="/siteSAE2A/deconnection"><i class="fa-solid fa-right-from-bracket"></i>
                    Déconnexion</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <header class="header-container">
            <h1>Tableau de bord</h1>
        </header>

        <div class="dashboard-grid">

            <section class="card stat-card planning-card">
                <h2>Nombre d'utilisateurs</h2>
                <div class="circle-chart" id="users-circle"
                    style="--final-value: <?= min($results["totalUsers"], 100) ?>;">
                    <span id="users-counter">0</span>
                </div>
            </section>

            <section class="card stat-card planning-card">
                <h2>Voiture la plus louée</h2>
                <?php if (!empty($results["voiturePlusLouee"])):
                    $bestCar = $results["voiturePlusLouee"]; ?>
                    <div class="best-car-container">
                        <div class="info">
                            <strong><?= htmlspecialchars($bestCar["Marque"]) ?></strong>
                            <span><?= htmlspecialchars($bestCar["Modele"]) ?></span>
                            <small>Louée <?= (int) $bestCar["nb_locations"] ?> fois</small>
                        </div>
                        <div class="image-wrapper">
                            <img src="<?= !empty($bestCar["ImagePath"]) ? htmlspecialchars($bestCar["ImagePath"]) : 'html/icons/voiture.png' ?>"
                                alt="Véhicule">
                        </div>
                    </div>
                <?php else: ?>
                    <p class="empty-data">Aucune donnée disponible</p>
                <?php endif; ?>
            </section>

            <section class="card stat-card planning-card">
                <h2>Revenus mensuels</h2>
                <div class="circle-chart" id="revenus-circle"
                    style="--final-value: <?= min(($results["revenusMensuels"] / 300000) * 100, 100) ?>;">
                    <div class="counter-container">
                        <span id="revenus-counter">0</span>€
                    </div>
                </div>
            </section>

            <section class="card large-card planning-card">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h2><i class="fa-solid fa-bell"></i> Rappels</h2>
                    <a href="#addRappelModal" class="add-btn-main"
                        style="text-decoration: none; font-size: 0.9em; padding: 5px 10px;">
                        <i class="fa-solid fa-plus"></i> Nouveau
                    </a>
                </div>

                <div class="scrollable-list">
                    <?php if (!empty($results['alerts'])): ?>
                        <?php foreach ($results['alerts'] as $alert): ?>
                            <div class="list-item">
                                <span class="badge">Alerte</span>
                                <p><?= htmlspecialchars($alert['label']) ?> –
                                    <strong><?= htmlspecialchars($alert['vehicule'] ?? '') ?></strong>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="list-item empty">✅ Aucun rappel en cours</div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="card large-card planning-card">
                <h2><i class="fa-solid fa-calendar-day"></i> Planning Proche</h2>
                <div class="scrollable-list">
                    <?php if (!empty($results['planning'])): ?>
                        <?php foreach ($results['planning'] as $event): ?>
                            <div class="list-item">
                                <span class="date-tag"><?= htmlspecialchars(date('d/m/Y', strtotime($event['time']))) ?></span>
                                <p><strong><?= htmlspecialchars($event['action']) ?></strong> :
                                    <?= htmlspecialchars($event['vehicule']) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="list-item empty">Aucun événement prévu</div>
                    <?php endif; ?>
                </div>
            </section>

        </div>

        <div id="addRappelModal" class="modal">
            <div class="modal-content">
                <a href="#" class="close">&times;</a>
                <h2>Ajouter un Rappel</h2>
                <form method="POST" action="/siteSAE2A/dashboard">
                    <input type="hidden" name="action" value="ajouterRappel">

                    <div class="form-group">
                        <input type="text" name="Titre" placeholder="Titre du rappel" required
                            style="width: 100%; margin-bottom: 15px; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                    </div>

                    <div class="form-group">
                        <input type="text" name="Description" placeholder="Description" required
                            style="width: 100%; margin-bottom: 15px; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                    </div>

                    <div class="form-group">
                        <input type="date" name="Date" required
                            style="width: 100%; margin-bottom: 15px; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                    </div>

                    <button type="submit" class="save-btn"
                        style="background: var(--primary-color, #007bff); color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; width: 100%;">Enregistrer
                        le rappel</button>
                </form>
            </div>
        </div>
    </main>

    <script>
        function animateCounter(id, targetValue, duration) {
            const counter = document.getElementById(id);
            if (!counter) return;

            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const currentValue = Math.floor(progress * targetValue);

                counter.textContent = currentValue.toLocaleString();

                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        document.addEventListener('DOMContentLoaded', () => {
            animateCounter("revenus-counter", <?= (float) $results["revenusMensuels"] ?>, 1200);
            animateCounter("users-counter", <?= (int) $results["totalUsers"] ?>, 1200);
        });
    </script>

</body>

</html>