<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur - RentPark</title>

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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/siteSAE2A/html/css/commun.css">
    <link rel="stylesheet" href="/siteSAE2A/html/css/erreur.css">
    
    <link rel="icon" type="image/png" href="html/icons/voiture.png">
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
    <div class="error-wrapper">
        <div class="error-card">
            <div class="error-icon">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>

            <h1>Oups ! Quelque chose a coincé</h1>

            <div class="error-msg-content">
                <?php 
                if (isset($dVueErreur) && !empty($dVueErreur)) {
                    foreach ($dVueErreur as $msg) {
                        echo htmlspecialchars($msg) . "<br>";
                    }
                } else {
                    echo "Une erreur inattendue est survenue lors de l'opération.";
                }
                ?>
            </div>

            <p style="opacity: 0.7; margin-bottom: 20px;">
                Vous pouvez réessayer l'action ou retourner à l'accueil.
            </p>

            <div class="actions">
                <a href="/siteSAE2A/home" class="btn-home">
                    <i class="fa-solid fa-house"></i> Retour à l'accueil
                </a>
            </div>

            <?php if (isset($results) && !empty($results)): ?>
                <div class="debug-section">
                    <button class="debug-btn" onclick="toggleDebug()">
                        <i class="fa-solid fa-bug"></i> Détails techniques pour le développeur
                    </button>
                    
                    <div id="debugConsole" class="debug-console">
                        <strong><i class="fa-solid fa-file-code"></i> Fichier :</strong> <?= $results['fichier'] ?? 'Inconnu' ?><br>
                        <strong><i class="fa-solid fa-list-ol"></i> Ligne :</strong> <?= $results['ligne'] ?? 'N/A' ?><br>
                        <hr style="border: 0.5px solid #444; margin: 10px 0;">
                        <strong><i class="fa-solid fa-terminal"></i> Stacktrace :</strong><br>
                        <pre style="white-space: pre-wrap; word-wrap: break-word;"><?= $results['trace'] ?? 'Aucune trace disponible' ?></pre>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleDebug() {
            const consoleDiv = document.getElementById('debugConsole');
            const isHidden = window.getComputedStyle(consoleDiv).display === 'none';
            consoleDiv.style.display = isHidden ? 'block' : 'none';
        }
    </script>

</body>
</html>