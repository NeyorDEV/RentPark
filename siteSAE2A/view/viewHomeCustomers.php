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
        <li><a href="/siteSAE2A/index.php"><i class="fa-solid fa-house"></i> Accueil</a></li>
        <li><a href="/siteSAE2A/voitures"><i class="fa-solid fa-car"></i> Flotte Automobile</a></li>
        <li><a href="/siteSAE2A/dashboard"><i class="fa-solid fa-dashboard"></i>Tableau de bord</a></li>
        <li><i class="fa-solid fa-file-signature"></i> Contrats</li>
        <li><a href="/siteSAE2A/reservation"><i class="fa-solid fa-calendar-days"></i> Réservations</a></li>
        <li><i class="fa-solid fa-user"></i> Utilisateurs</li>
        <li><a href="/siteSAE2A/deconnection"><i class="fa-solid fa-gear"></i> Paramètres</a></li>
    </ul>
</aside>

<!-- ========== HEADER + FORMULAIRE ========== -->
<header>
     <a href="/siteSAE2A/connection">
        <div class="top-right-btn">
            <div class="circle"></div>
            <span>Connection/Inscription</span>
        </div>
    </a>
    <h1>RENTPARK</h1>

    <div class="search-box">

        <div class="row">

            <div class="input-block">
                <label>Date de départ</label>
                <div class="dual">
                    <div class="input-icon">
                        <i class="fa-solid fa-calendar"></i>
                        <input type="date">
                    </div>
                </div>
            </div>

            <div class="input-block">
                <label>Date de retour</label>
                <div class="dual">
                   <div class="input-icon">
                        <i class="fa-solid fa-calendar"></i>
                        <input type="date">
                    </div>
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
