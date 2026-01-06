<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Rentpark</title>
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
        <li><a href="#"><i class="fa-solid fa-file-signature"></i> Contrats</a></li>
        <li><a href="/siteSAE2A/reservation"><i class="fa-solid fa-calendar-days"></i> Réservations</a></li>
        <li><a href="#"><i class="fa-solid fa-user"></i> Utilisateurs</a></li>
        <li><a href="/siteSAE2A/parametres"><i class="fa-solid fa-gear"></i> Paramètres</a></li>
    </ul>
</aside>

<!-- ========== HEADER + FORMULAIRE ========== -->
<header>
    <a href="/siteSAE2A/connection">
        <div class="top-right-btn">
            <div class="circle"></div>
            <span>Connexion/Inscription</span>
        </div>
    </a>
    <h1 id="RentPark">RENTPARK</h1>

    <form action="/siteSAE2A/cars" method="GET" class="search-box">

        <div class="row">

            <div class="input-block">
                <label for="date_depart">Date de départ</label>
                <div class="dual">
                    <div class="input-icon">
                        <i class="fa-solid fa-calendar"></i>
                        <input type="date" name="date_depart" id="date_depart" required>
                    </div>
                </div>
            </div>

            <div class="input-block">
                <label for="date_retour">Date de retour</label>
                <div class="dual">
                   <div class="input-icon">
                        <i class="fa-solid fa-calendar"></i>
                        <input type="date" name="date_retour" id="date_retour" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-orange">Voir les véhicules</button>
        </div>
    </form>
</header>

<script>
function toggleMenu() {
    document.getElementById("sidebar").classList.toggle("open");
}
</script>

</body>
</html>
