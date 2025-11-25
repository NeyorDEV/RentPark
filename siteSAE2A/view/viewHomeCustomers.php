<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Rentpark</title>
    <link rel="stylesheet" href="html/css/homeCustomers.css">
</head>
<body>

<!-- ========== MENU BURGER ========== -->
<div class="burger" onclick="toggleMenu()">
    <span></span>
    <span></span>
    <span></span>
</div>

<!-- ========== SIDEBAR ========== -->
<aside id="sidebar">
    <ul>
        <li><i class="fa-solid fa-house"></i> Accueil</li>
        <li><i class="fa-solid fa-chart-line"></i> Tableau de bord</li>
        <li><i class="fa-solid fa-car"></i> Flotte Automobile</li>
        <li><i class="fa-solid fa-file-signature"></i> Contrats</li>
        <li><i class="fa-solid fa-calendar-days"></i> Réservation</li>
        <li><i class="fa-solid fa-user"></i> Utilisateur</li>
        <li><i class="fa-solid fa-gear"></i> Paramètres</li>
        <li><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</li>
    </ul>
</aside>

<!-- ========== HEADER + FORMULAIRE ========== -->
<header>
    <div class="top-right-btn">
        <div class="circle"></div>
        <span>Connection/Inscription</span>
    </div>

    <h1>Rentpark</h1>

    <div class="search-box">

        <div class="row">

            <div class="input-block">
                <label>Date de départ</label>
                <div class="dual">
                    <div class="input-icon">
                        <i class="fa-solid fa-calendar"></i>
                        <input type="date">
                    </div>
                    <input type="time" value="12:30">
                </div>
            </div>

            <div class="input-block">
                <label>Date de retour</label>
                <div class="dual">
                    <div class="input-icon">
                        <i class="fa-solid fa-calendar"></i>
                        <input type="date">
                    </div>
                    <input type="time" value="08:30">
                </div>
            </div>

            <button class="btn-orange">Voir les véhicules</button>
        </div>
    </div>
</header>

<script>
function toggleMenu() {
    document.getElementById("sidebar").classList.toggle("open");
}
</script>

</body>
</html>
