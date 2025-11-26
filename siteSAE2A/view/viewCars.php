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
        <span>Paris CDG Aéroport Terminal 2</span>
        <span>27 nov. | 12:30 - 01 déc. | 08:30</span>
    </div>
    <h1>Quelle voiture voulez-vous conduire ?</h1>
</header>

<!-- Filter options -->
<div class="filter-options">
    <button class="active">Par Prix le plus bas</button>
    <button>Filtres</button>
    <button><i class="fa-solid fa-car"></i> Automatique</button>
    <button><i class="fa-solid fa-bolt"></i> Électrique</button>
</div>

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
