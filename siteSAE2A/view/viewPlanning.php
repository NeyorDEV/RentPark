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

    <style>
        /* Style du modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            position: relative;
        }
        .modal-close {
            position: absolute;
            top: 10px; right: 10px;
            cursor: pointer;
            font-size: 18px;
            color: #333;
        }
    </style>
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
            <a href="?month=<?= $results['prevMonth'] ?>&year=<?= $results['prevYear'] ?>">← Mois précédent</a>
            <strong><?= htmlspecialchars($results['currentMonthLabel']) ?></strong>
            <a href="?month=<?= $results['nextMonth'] ?>&year=<?= $results['nextYear'] ?>">Mois suivant →</a>
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
                                <span class="event-letter <?= $event['type'] ?>"
                                      title="<?= htmlspecialchars($event['label']) ?>"
                                      data-id="<?= $event['idContrat'] ?>"
                                      data-client="<?= htmlspecialchars($event['client']) ?>"
                                      data-vehicule="<?= htmlspecialchars($event['vehicule']) ?>"
                                      data-date-debut="<?= $event['dateDebut'] ?>"
                                      data-date-fin="<?= $event['dateFin'] ?>"
                                      data-statut="<?= $event['statut'] ?>">
                                    <?= $event['type'] === 'depart' ? 'D' : 'R' ?>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="eventModal" class="modal">
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        <h3>Détails de la réservation</h3>
        <p id="modalContent"></p>
    </div>
</div>

<script>
    // Sélection des événements
    const events = document.querySelectorAll('.event-letter');
    const modal = document.getElementById('eventModal');
    const modalContent = document.getElementById('modalContent');
    const closeBtn = modal.querySelector('.modal-close');

    events.forEach(ev => {
        ev.addEventListener('click', () => {
            const id = ev.dataset.id;
            const client = ev.dataset.client;
            const vehicule = ev.dataset.vehicule;
            const debut = ev.dataset.dateDebut;
            const fin = ev.dataset.dateFin;
            const statut = ev.dataset.statut;

            modalContent.innerHTML = `
                <strong>ID Contrat:</strong> ${id} <br>
                <strong>Client:</strong> ${client} <br>
                <strong>Véhicule:</strong> ${vehicule} <br>
                <strong>Du:</strong> ${debut} <br>
                <strong>Au:</strong> ${fin} <br>
                <strong>Statut:</strong> ${statut}
            `;
            modal.style.display = 'flex';
        });
    });

    closeBtn.addEventListener('click', () => modal.style.display = 'none');
    window.addEventListener('click', e => { if(e.target === modal) modal.style.display = 'none'; });
</script>

</body>
</html>
