<?php
 $isAdmin = ($role === 'admin');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>RentPark - Reservation</title>
    <link rel="stylesheet" href="html/css/reservation.css">
</head>
<body>   

<nav>
    <ul class="menu">
        <li><a href=""><img src="html/icons/acceuil.jpg" alt="Accueil"> Accueil</a></li>
        <li><a href=""><img src="html/icons/voiture.png" alt="Flotte"> Flotte Automobile</a></li>
        <li><a href=""><img src="html/icons/contrat.png" alt="Contrats"> Contrats</a></li>
        <li><a href="index.php?action=listeReservation"><img src="html/icons/contrat.png" alt="Réservations"> Réservations</a></li>
        <li><a href=""><img src="html/icons/settings.png" alt="Paramètres"> Paramètres</a></li>
        
    </ul>       
</nav>

<div class="top"> 
    <h1>Reservation</h1>
    <?php if ($isAdmin): ?>
        <a href="#addModal" class="add-btn">+ Ajouter</a>
    <?php endif; ?>
</div>


<header class="topbar">
  <form action="index.php" method="get" role="search" class="topbar-form">
    <input type="hidden" name="action" value="rechercherVoitures">

    <input
      id="q3"
      name="q"
      type="search"
      placeholder="Rechercher..."
      aria-label="Recherche"
      value="<?php echo htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES); ?>"
    >

    <button type="submit">🔍</button>
  </form>
</header>



<?php if (!empty($dVueErreur)) : ?>
    <div class="erreurs">
        <ul>
            <?php foreach ($dVueErreur as $erreur) : ?>
                <li><?= htmlspecialchars($erreur) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="reservation">
    <?php if (!empty($results)) : ?>
        <?php foreach ($results as $row) : ?>
            <div class="rectangle">
                <p>
                    <?= htmlspecialchars($row['idContrat']) ?><br>
                    <?= htmlspecialchars($row['vehiculeVin']) ?> cv<br>
                    <?= htmlspecialchars($row['dateDebut']) ?><br>
                    <?= htmlspecialchars($row['dateFin']) ?><br>
                </p>

                <!-- Formulaire pour supprimer -->
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <div class="rectangle">
            <p>Aucune réservation trouvée</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>