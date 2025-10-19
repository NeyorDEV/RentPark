<?php
// $results est fourni par le contrôleur
// $dVueErreur contient les messages d'erreur si besoin
// action à modifier dans la top bar 

 
 $isAdmin = ($role === 'admin');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>RentPark - Flotte Automobile</title>
    <link rel="stylesheet" href="html/css/menu.css">
</head>
<body>   

<nav>
    <ul class="menu">
        <li><a href=""><img src="html/icons/acceuil.jpg" alt="Accueil"> Accueil</a></li>
        <li><a href=""><img src="html/icons/voiture.png" alt="Flotte"> Flotte Automobile</a></li>
        <li><a href=""><img src="html/icons/contrat.png" alt="Contrats"> Contrats</a></li>
        <li><a href="view/viewUtilisateur.php"><img src="html/icons/settings.png" alt="Utilisateurs"> Utilisateur</a></li>
        <li><a href=""><img src="html/icons/settings.png" alt="Paramètres"> Paramètres</a></li>
    </ul>       
</nav>

<div class="top"> 
    <h1>Flotte Automobile</h1>
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

<div class="voiture">
    <?php if (!empty($results)) : ?>
        <?php foreach ($results as $row) : ?>
            <div class="rectangle">
                <p>
                    <?= htmlspecialchars($row['modele']) ?><br>
                    <?= htmlspecialchars($row['puissance']) ?> cv<br>
                    <?= htmlspecialchars($row['couleur']) ?><br>
                    <img src="html/icons/voiture.png" alt="Voiture" width="100px">
                </p>

                <!-- Formulaire pour supprimer -->
                <form method="POST" action ="index.php" onsubmit="return confirm('Supprimer ce véhicule ?');">
                    <input type="hidden" name="action" value="supprimerVoiture">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($row['voiture']) ?>">
                    <button type="submit"  class="delete-btn">Supprimer</button>
                </form>
                
            <button type="button" class="delete-btn" onclick="location.hash='editModal-<?= htmlspecialchars($row['voiture']) ?>'">Modifier</button>
            </div>
            <div id="editModal-<?= htmlspecialchars($row['voiture']) ?>" class="modal">
                <div class="modal-content">
                    <a href="#" class="close">×</a>
                    <h2>Modifier le véhicule</h2>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="action" value="modifierVoiture">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($row['voiture']) ?>">

                        <label>Modèle<br>
                            <input type="text" name="modele" required value="<?= htmlspecialchars($row['modele']) ?>">
                        </label>
                        <br><br>

                        <label>Puissance (cv)<br>
                            <input type="number" name="puissance" required value="<?= htmlspecialchars($row['puissance']) ?>">
                        </label>
                        <br><br>

                        <label>Couleur<br>
                            <input type="text" name="couleur" required value="<?= htmlspecialchars($row['couleur']) ?>">
                        </label>
                        <br><br>

                        <button type="submit">Enregistrer</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <div class="rectangle">
            <p>Aucun véhicule trouvé</p>
        </div>
    <?php endif; ?>
</div>

<!-- Modal pour ajouter -->

<div id="addModal" class="modal">
    <div class="modal-content">
        <a href="#" class="close">&times;</a>
        <h2>Ajouter un véhicule</h2>
        <form method="POST" action="index.php">
             <input type="hidden" name="action" value="ajouterVoiture">
            <input type="text" name="modele" placeholder="Modèle" required><br><br>
            <input type="number" name="puissance" placeholder="Puissance" required><br><br>
            <input type="text" name="couleur" placeholder="Couleur" required><br><br>
                <button type="submit" name="ajouterVoiture">Ajouter</button>

        </form>
    </div>
</div>


</body>
</html>

