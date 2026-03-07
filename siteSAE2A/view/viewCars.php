<?php
// Récupération des dates et des résultats
$date_depart = $_GET['date_depart'] ?? ($date_depart ?? null);
$date_retour = $_GET['date_retour'] ?? ($date_retour ?? null);
$voitures = $results ?? [];

// --- LOGIQUE DE TRI ---
if (!empty($voitures) && isset($_GET['tri'])) {
    $ordre = $_GET['tri']; // 'asc' ou 'desc'
    
    usort($voitures, function($a, $b) use ($ordre) {
        // On récupère les prix. Si la clé 'Prix' n'existe pas, on utilise 0 par défaut.
        $prixA = $a['Prix'] ?? 0;
        $prixB = $b['Prix'] ?? 0;

        if ($prixA == $prixB) {
            return 0;
        }

        if ($ordre === 'desc') {
            return ($prixA > $prixB) ? -1 : 1;
        } else {
            return ($prixA < $prixB) ? -1 : 1;
        }
    });
}
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
    <link rel="shortcut icon" href="html/icons/favicon.ico" type="image/x-icon">
</head>
<body>

<header>
    <div class="filtre" onclick="toggleMenu()" >
        <span><i class="fa-solid fa-filter"></i> Filtres</span>
    </div>
    <div class="filtre "  style="left: 130px;">
        <a class="home" href="/siteSAE2A/home"><i class="fa-solid fa-house"></i> Accueil</a>
    </div>
    
    <a href="/siteSAE2A/connection">
        <div class="top-right-btn">
            <div class="circle"></div>
            <span>Connection/Inscription</span>
        </div>
    </a>

    <div class="top-bar">
        <span><i class="fa-solid fa-calendar-alt"></i> Votre réservation :</span>
        <span>
            <?php if (!empty($date_depart) && !empty($date_retour)): ?>
                <a href="/siteSAE2A/index.php"
           class="reservation-click"
           title="Modifier la réservation">
            Du <strong><?php echo htmlspecialchars($date_depart); ?></strong>
            au <strong><?php echo htmlspecialchars($date_retour); ?></strong>
        </a>
            
            </div>
            <?php else: ?>
                Dates non sélectionnées
            <?php endif; ?>
        </span>
    </div>
    <h1>Quelle voiture voulez-vous conduire ?</h1>
</header>

<aside id="sidebar">
    <form action="/siteSAE2A/cars" method="GET">
        <input type="hidden" name="date_depart" value="<?php echo htmlspecialchars($date_depart); ?>">
        <input type="hidden" name="date_retour" value="<?php echo htmlspecialchars($date_retour); ?>">

        <div class="sidebar-header">
            <button type="button" class="close-btn" onclick="toggleMenu()">✖</button>
            <h2>Filtres</h2>
            <button type="reset" class="clear-btn" onclick="window.location.href='/siteSAE2A/cars?date_depart=<?php echo $date_depart; ?>&date_retour=<?php echo $date_retour; ?>'">Effacer</button>
        </div>
        
        <div class="filter-option">
            <div class="fliter-label"><label>Prix (€)</label></div>
            <div class="price-inputs">
                <input type="number" name="prix_min" placeholder="Min" value="<?php echo htmlspecialchars($_GET['prix_min'] ?? ''); ?>">
                <input type="number" name="prix_max" placeholder="Max" value="<?php echo htmlspecialchars($_GET['prix_max'] ?? ''); ?>">
            </div>
        </div>
            
            <div class="filter-btn-group-vertical">
                <input type="radio" name="tri" value="asc" id="tri-asc" <?php if(($_GET['tri'] ?? '') == 'asc') echo 'checked'; ?>>
                <label for="tri-asc">Prix croissant</label>

                <input type="radio" name="tri" value="desc" id="tri-desc" <?php if(($_GET['tri'] ?? '') == 'desc') echo 'checked'; ?>>
                <label for="tri-desc">Prix décroissant</label>
            </div>
        </div>
        
        <div class="filter-option">
            <div class="fliter-label"><label>Boîte</label></div>
            <div class="filter-btn-group-vertical">
                <input type="radio" name="boite" value="Automatique" id="b-auto" <?php if(($_GET['boite'] ?? '') == 'Automatique') echo 'checked'; ?>>
                <label for="b-auto">Automatique</label>

                <input type="radio" name="boite" value="Manuelle" id="b-manuel" <?php if(($_GET['boite'] ?? '') == 'Manuelle') echo 'checked'; ?>>
                <label for="b-manuel">Manuelle</label>
            </div>
        </div>

        <div class="filter-option">
            <div class="fliter-label"><label>Énergie</label></div>
            <div class="filter-btn-group-vertical">
                <input type="radio" name="energie" value="Essence" id="e-essence" <?php if(($_GET['energie'] ?? '') == 'Essence') echo 'checked'; ?>>
                <label for="e-essence">Essence</label>

                <input type="radio" name="energie" value="Diesel" id="e-diesel" <?php if(($_GET['energie'] ?? '') == 'Diesel') echo 'checked'; ?>>
                <label for="e-diesel">Diesel</label>

                <input type="radio" name="energie" value="Électrique" id="e-elec" <?php if(($_GET['energie'] ?? '') == 'Électrique') echo 'checked'; ?>>
                <label for="e-elec">Électrique</label>

                <input type="radio" name="energie" value="Hybride" id="e-hybride" <?php if(($_GET['energie'] ?? '') == 'Hybride') echo 'checked'; ?>>
                <label for="e-hybride">Hybride</label>
            </div>
        </div>

        <button type="submit" class="btn-orange">Afficher les offres</button>
    </form>
</aside>

<div class="cars-section">
    <?php if (!empty($voitures)): ?>
        <?php foreach ($voitures as $voiture): ?>
    <div class="car-card">
        <?php
        $image_path = $voiture['image_path'] ?? $voiture['ImagePath'] ?? 'default.jpg';
        if (empty($image_path)) {
            $image_path ='html/icons/car.png';
        }
        ?>
        <img src="/siteSAE2A/<?php echo htmlspecialchars($image_path); ?>" alt="Image voiture">
        
        <div class="car-info">
            <h3><?php echo htmlspecialchars($voiture['Nom']); ?></h3>
            <p>Marque : <?php echo htmlspecialchars($voiture['Marque']); ?></p>
            
            <p>
                Couleur : <?php echo htmlspecialchars($voiture['Couleur']); ?> | 
                Boîte : <strong><?php echo htmlspecialchars($voiture['Boite'] ?? 'N/C'); ?></strong> | 
                Énergie : <strong><?php echo htmlspecialchars($voiture['Energie'] ?? 'N/C'); ?></strong> |
                Puissance : <?php echo htmlspecialchars($voiture['Puissance']); ?>
            </p>
            
            <span class="price">
                <?php echo isset($voiture['Prix']) ? htmlspecialchars($voiture['Prix']) . " € / jour" : "Prix à définir"; ?>
            </span>

            <form action="/siteSAE2A/reservationForm" method="GET" style="width: 100%; margin-top: 10px;">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($voiture['NumSerie']); ?>">
                <input type="hidden" name="date_depart" value="<?php echo htmlspecialchars($date_depart); ?>">
                <input type="hidden" name="date_retour" value="<?php echo htmlspecialchars($date_retour); ?>">

                <button type="submit" class="btn-orange" 
                        style="border: none;
                               cursor: pointer;
                               display: flex; 
                               align-items: center; 
                               justify-content: center; 
                               text-align: center; 
                               height: 40px; 
                               width: 100%; 
                               box-sizing: border-box;
                               font-size: 1rem;
                               font-weight: bold;">
                    Réserver
                </button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align: center; width: 100%;">Aucune voiture ne correspond à vos critères.</p>
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