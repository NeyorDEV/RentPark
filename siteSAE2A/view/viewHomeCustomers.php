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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="html/css/homeCustomers.css">
    <link rel="shortcut icon" href="html/icons/favicon.ico" type="image/x-icon">

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
        <?php endif;?>
    </ul>
    <?php if ($role !== 'unknown') : ?>
            <div class="logout-item"><a href="/siteSAE2A/deconnection"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a></div>
        <?php endif;?>
</aside>

<!-- ========== HEADER + FORMULAIRE ========== -->



<header> 
    <?php if ($role === 'unknown') : ?>
    <a href="/siteSAE2A/connection">
        <div class="top-right-btn">
            <div class="circle"></div>
            <span>Connexion/Inscription</span>
        </div>
    </a>
    <?php else :?>
        <div class="top-right">
            <div class="circle"></div>
            <span>Connecté en tant que : <?php echo $role?></span>
        </div>
    <?php endif; ?>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateDepart = document.getElementById('date_depart');
    const dateRetour = document.getElementById('date_retour');
    const form = document.querySelector('.search-box');

    // 1. Définir la date minimale (Aujourd'hui) pour le départ
    const today = new Date().toISOString().split('T')[0];
    dateDepart.setAttribute('min', today);
    dateRetour.setAttribute('min', today);

    // 2. Quand la date de départ change, on met à jour le minimum de la date de retour
    dateDepart.addEventListener('change', function() {
        if (dateDepart.value) {
            // Le retour doit être au minimum le même jour (ou le lendemain selon votre choix)
            dateRetour.setAttribute('min', dateDepart.value);
            
            // Si la date de retour actuelle est avant la nouvelle date de départ, on la réinitialise
            if (dateRetour.value && dateRetour.value < dateDepart.value) {
                dateRetour.value = dateDepart.value;
            }
        }
    });

    // 3. Sécurité supplémentaire lors de la soumission du formulaire
    form.addEventListener('submit', function(e) {
        const start = new Date(dateDepart.value);
        const end = new Date(dateRetour.value);
        const now = new Date();
        now.setHours(0,0,0,0); // On ne compare que la date, pas l'heure

        if (start < now) {
            alert("La date de départ ne peut pas être dans le passé.");
            e.preventDefault();
        } else if (end < start) {
            alert("La date de retour doit être après la date de départ.");
            e.preventDefault();
        }
    });
});

function toggleMenu() {
    document.getElementById("sidebar").classList.toggle("open");
}
</script>
</body>
</html>
