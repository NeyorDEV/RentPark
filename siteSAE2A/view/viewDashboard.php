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

</head>
<body>

<nav>
    <ul class="menu">
        <li><a href="/siteSAE2A/index.php"><img src="html/icons/acceuil.jpg" alt="Accueil"> Accueil</a></li>
        <li><a href=""><img src="html/icons/dashboard.png" alt="Tableau de bord"> Tableau de bord</a></li>
        <li><a href="/sitesae2A/voitures"><img src="html/icons/voiture.png" alt="Flotte"> Flotte Automobile</a></li>
        <li><a href=""><img src="html/icons/contrat.png" alt="Contrats"> Contrats</a></li>
        <li><a href="/siteSAE2A/reservation"><img src="html/icons/reservation.png" alt="Réservation"> Réservation</a></li>
        <li><a href="siteSAE2A/../utilisateurs"><img src="html/icons/user.png" alt="Utilisateurs"> Utilisateur</a></li>
        <li><a href="/siteSAE2A/parametres"><img src="html/icons/settings.png" alt="Paramètres"> Paramètres</a></li>
        <li><a href="/siteSAE2A/deconnection"><img src="html/icons/logout.png" alt="se déconnecter"> déconnexion</a></li>
    </ul>
</nav>


<!-- ZONE PRINCIPALE -->
<div class="main-content">
    <h1>Tableau de bord</h1>
    <div class="dashboard-cards">
        <!-- Exemple de carte statistique -->
        <div class="card">
    <h2>Nombre d'utilisateurs</h2>
    <div class="circle-chart" 
         id="users-circle" 
         style="--final-value: <?= min($results["totalUsers"], 100) ?>;">
        <span id="users-counter">0</span>
    </div>
</div>


        <div class="card">
    <h2>Voiture la plus louée</h2>
    <?php if (!empty($results["voiturePlusLouee"])) : 
    $bestCar = $results["voiturePlusLouee"];
?>
    <div class="best-car-card">
        <div class="info">
            <p>
                <strong><?= htmlspecialchars($bestCar["Marque"]) ?></strong>
                <?= htmlspecialchars($bestCar["Modele"]) ?><br>
                <span class="small">Louée <?= (int)$bestCar["nb_locations"] ?> fois</span>
            </p>
        </div>
        <div class="image-container">
            <?php if (!empty($bestCar["ImagePath"])) : ?>
                <img src="<?= htmlspecialchars($bestCar["ImagePath"]) ?>" alt="Image de <?= htmlspecialchars($bestCar["Modele"]) ?>">
            <?php else : ?>
                <img src="html/icons/voiture.png" alt="Voiture par défaut">
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

</div>



        <div class="card">
    <h2>Revenus mensuels</h2>
    <div class="circle-chart" 
         id="revenus-circle" 
         style="--final-value: <?= min(($results["revenusMensuels"] / 300000) * 100, 100) ?>;">
        <span id="revenus-counter">0</span>€
    </div>
</div>

<script>
function animateCounter(id, targetValue, duration) {
    const counter = document.getElementById(id);
    let start = 0;
    const fps = 60; // frames par seconde
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

// Exemple : chiffre d'affaires de PHP
const revenusMensuels = <?= $results["revenusMensuels"] ?>;

animateCounter("revenus-counter", revenusMensuels, 800); // 0,8s pour atteindre la valeur


    // Animation pour le nombre d'utilisateurs
const totalUsers = <?= $results["totalUsers"] ?>;
animateCounter("users-counter", totalUsers, 800); // 0,8s pour atteindre la valeur

</script>








   
 <div class="bottom-cards">   
    <div class="alert">
        <h2>Rappel</h2>
        

     <div class="alert-content">
        <div class="rectangle">Contrôle Technique – BMW Série 4</div>
        <div class="rectangle">Vidange – Clio 3</div>
        <div class="rectangle">Assurance – Tesla Model 3</div>
        <div class="rectangle">Contrôle Pollution – Peugeot 208</div>
        <div class="rectangle">Révision – Audi A3</div>
        <div class="rectangle">Révision – Audi A3</div>
        <div class="rectangle">Révision – Audi A3</div>
        <div class="rectangle">Révision – Audi A3</div>
        <div class="rectangle">Révision – Audi A3</div>
        <div class="rectangle">Révision – Audi A3</div>
        <div class="rectangle">Révision – Audi A3</div>
        <div class="rectangle">Révision – Audi A3</div>
        </div>
    </div>

<div class="planning">
    <h2>Planning</h2>
    <div class="planning-content">
        <div class="rectangle">09:00 – Location Peugeot 208</div>
        <div class="rectangle">10:30 – Retour Tesla Model 3</div>
        <div class="rectangle">13:00 – Location BMW Série 4</div>
        <div class="rectangle">16:00 – Nettoyage Renault Twingo</div>
        <div class="rectangle">18:00 – Clôture caisse</div>
        <div class="rectangle">18:00 – Clôture caisse</div>
        <div class="rectangle">18:00 – Clôture caisse</div>
        <div class="rectangle">18:00 – Clôture caisse</div>
        <div class="rectangle">18:00 – Clôture caisse</div>
        <div class="rectangle">18:00 – Clôture caisse</div>
        <div class="rectangle">18:00 – Clôture caisse</div>
        <div class="rectangle">18:00 – Clôture caisse</div>
        <div class="rectangle">18:00 – Clôture caisse</div>
        <div class="rectangle">18:00 – Clôture caisse</div>
    </div>
</div>

</div>
</div>
</div>

</body>
</html>


