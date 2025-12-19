<?php
$date_depart = $_GET['date_depart'] ?? ($date_depart ?? null);
$date_retour = $_GET['date_retour'] ?? ($date_retour ?? null);
$voitures = $results ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rentpark - Choisir une voiture</title>
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
    
    <link rel="stylesheet" href="/siteSAE2A/html/css/cars.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>

<header>
    <div class="top-bar">
        <span><i class="fa-solid fa-calendar-alt"></i> Votre réservation :</span>
        <span>
            <?php if (!empty($date_depart) && !empty($date_retour)): ?>
                Du <strong><?php echo htmlspecialchars($date_depart); ?></strong> au <strong><?php echo htmlspecialchars($date_retour); ?></strong>
            <?php else: ?>
                Dates non sélectionnées
            <?php endif; ?>
        </span>
    </div>
    <h1>Quelle voiture voulez-vous conduire ?</h1>

    
    <div class="filtre" onclick="toggleMenu()">
        <span><i class="fa-solid fa-filter"></i> Filtres</span>
    </div>
</header>

<aside id="sidebar">
    <div class="sidebar-header">
        <button class="close-btn" onclick="toggleMenu()">✖</button>
        <h2>Filtres</h2>
    </div>
    
    <div class="filter-option">
        <div class="fliter-label"><label>Trié par prix</label></div>
        <div class="filter-btn-group">
            <button>Par prix le plus bas</button>
            <div class="price-max">
                <label for="max-price">Max :</label>
                <input type="number" id="max-price" placeholder="Prix Max">
            </div>
        </div>
    </div>
    
    <div class="filter-option">
        <div class="fliter-label"><label>Boîte</label></div>
        <div class="filter-btn-group">
            <button>Automatique</button>
            <button>Manuelle</button>
        </div>
    </div>

    <div class="filter-option">
        <div class="fliter-label"><label>Energie</label></div>
        <div class="filter-btn-group">
            <button>Diesel</button>
            <button>Essence</button>
            <button>Hybride</button>
            <button>Electrique</button>
        </div>
    </div>

    <button class="btn-orange">Afficher les offres</button>
</aside>

<div class="cars-section">
    <?php if (!empty($voitures)): ?>
        <?php foreach ($voitures as $voiture): ?>
    <div class="car-card">
        <img src="/siteSAE2A/html/img/<?php echo htmlspecialchars($voiture['ImagePath'] ?? 'default.jpg'); ?>" alt="Image voiture">
        <div class="car-info">
            <h3><?php echo htmlspecialchars($voiture['Nom']); ?></h3> <p>Marque : <?php echo htmlspecialchars($voiture['Marque']); ?></p>
            <p>Couleur : <?php echo htmlspecialchars($voiture['Couleur']); ?> | Puissance : <?php echo htmlspecialchars($voiture['Puissance']); ?></p>
            
            <span class="price">Prix à définir € / jour</span>
            <button class="btn-orange">Réserver</button>
        </div>
    </div>
<?php endforeach; ?>
    <?php else: ?>
        <div class="car-card">
            <img src="car_image_1.jpg" alt="Citroën E-C3">
            <div class="car-info">
                <h3>Citroën E-C3 ou similaire</h3>
                <p>Citadine SUV Automatique</p>
                <p><span>293km</span> | <span>4 Sièges</span> | <span>2 Bagages</span></p>
                <span class="price">22,74 € / jour</span>
                <button class="btn-orange">Réserver</button>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleMenu() {
    const sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("open");
}
</script>

</body>
</html>