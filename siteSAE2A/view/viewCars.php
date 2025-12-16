<?php
$dateDepart = $_GET['dateDepart'] ?? null;
$dateRetour = $_GET['dateRetour'] ?? null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rentpark - Choisir une voiture</title>
    <link rel="stylesheet" href="html/css/cars.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<!-- Header -->
<header>
    <div class="top-bar">
        <span>Paramètres :</span>
        <span>
            <?php if ($dateDepart && $dateRetour): ?>
                Du <?= htmlspecialchars($dateDepart) ?> au <?= htmlspecialchars($dateRetour) ?>
            <?php elseif ($dateDepart): ?>
                À partir du <?= htmlspecialchars($dateDepart) ?>
            <?php else: ?>
                Aucune date sélectionnée
            <?php endif; ?>
        </span>
    </div>
    <h1>Quelle voiture voulez-vous conduire ?</h1>

    <div class="filtre" onclick="toggleMenu()">
        <span>Filtre</span>
    </div>

    <script>
    function toggleMenu() {
        document.getElementById("sidebar").classList.toggle("open");
    }
    </script>
    
</header>

<body>
<aside id="sidebar">
    <div class="sidebar-header">
        <button class="close-btn" onclick="toggleMenu()">✖</button>
        <h2>Filtres</h2>
    </div>
    
    <!-- Trié par prix -->
    <div class="filter-option">
        <div class="fliter-label">
        <label>Trié par prix</label>
        </div>
        <div class="filter-btn-group">
            <button>Par prix le plus bas</button>
            <div class="price-max">
                <label for="max-price">Max :</label>
                <input type="number" id="max-price" placeholder="Prix Max">
            </div>
        </div>
    </div>
    
    <!-- Boîte -->
    <div class="filter-option">
        <div class="fliter-label">
        <label>Boîte</label>
        </div>
        <div class="filter-btn-group">
            <button>Automatique</button>
            <button>Manuelle</button>
        </div>
    </div>
    
    <!-- Energie -->
    <div class="filter-option">
        <div class="fliter-label">
        <label>Energie</label>
        </div>
        <div class="filter-btn-group">
            <button>Diesel</button>
            <button>Essence</button>
            <button>Hybride</button>
            <button>Electrique</button>
        </div>
    </div>
    
    <!-- Type de véhicule -->
    <div class="filter-option">
        <div class="fliter-label">
        <label>Type de Véhicule</label>
        </div>
        <div class="filter-btn-group">
            <button>SUV</button>
            <button>Sportive</button>
            <button>Citadine</button>
            <button>Coupé</button>
        </div>
    </div>
    
    <!-- Transmission -->
    <div class="filter-option">
        <div class="fliter-label">
        <label>Transmission</label>
        </div>
        <div class="filter-btn-group">
            <button>Traction</button>
            <button>Propulsion</button>
            <button>Intégrale</button>
        </div>
    </div>
    
    <!-- Nombre de places -->
    <div class="filter-option">
        <div class="fliter-label">
        <label>Nombre de places</label>
        </div>
        <div class="filter-btn-group">
            <input type="number" placeholder="Nombre de places">
        </div>
    </div>
    
    <!-- Puissance -->
    <div class="filter-option">
        <div class="fliter-label">
        <label>Puissance</label>
        </div>
        <div class="filter-btn-group">
            <input type="number" placeholder="Puissance en CV">
        </div>
    </div>
    
    <!-- Apply Filters Button -->
    <button class="btn-orange">Afficher les offres</button>
</aside>



<!-- Cars Section -->
<div class="cars-section">
    <!-- Car Card 1 -->
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

    <!-- Car Card 2 -->
    <div class="car-card">
        <img src="car_image_2.jpg" alt="VW Polo">
        <div class="car-info">
            <h3>VW Polo ou similaire</h3>
            <p>Citadine Berline Manuelle</p>
            <p><span>350km</span> | <span>5 Sièges</span> | <span>3 Bagages</span></p>
            <span class="price">27,32 € / jour</span>
            <button class="btn-orange">Réserver</button>
        </div>
    </div>

    <!-- Car Card 3 -->
    <div class="car-card">
        <img src="car_image_3.jpg" alt="Opel Mokka Electric">
        <div class="car-info">
            <h3>Opel Mokka Electric ou similaire</h3>
            <p>Compact SUV Automatique</p>
            <p><span>340km</span> | <span>5 Sièges</span> | <span>3 Bagages</span></p>
            <span class="price">27,32 € / jour</span>
            <button class="btn-orange">Réserver</button>
        </div>
    </div>
</div>

<!-- Car Details Modal -->
<div class="car-details">
    <div class="details-content">
        <img src="car_image_1.jpg" alt="Citroën E-C3">
        <div class="details-info">
            <h3>Citroën E-C3</h3>
            <p>Citadine SUV Automatique</p>
            <p>Gamme : 293km | 4 Sièges | 2 Bagages</p>
            <p>Âge minimum du conducteur : 18 ans</p>
            <button class="btn-orange">Suivant</button>
        </div>
    </div>
</div>

</body>
</html>
