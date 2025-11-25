<?php



 $isAdmin = ($role === 'admin');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>RentPark - Utilisateurs</title>
    <link rel="stylesheet" href="html/css/menu.css">
    <link rel="icon" type="image/png" href="html/icons/voiture.png">
    <link rel="shortcut icon" href="html/icons/favicon.ico" type="image/x-icon">
</head>
<body>

<nav>
    <ul class="menu">
        <li><a href=""><img src="html/icons/acceuil.jpg" alt="Accueil"> Accueil</a></li>
        <li><a href="/siteSAE2A/dashboard"><img src="html/icons/dashboard.png" alt="Tableau de bord"> Tableau de bord</a></li>
        <li><a href="/sitesae2A/voitures"><img src="html/icons/voiture.png" alt="Flotte"> Flotte Automobile</a></li>
        <li><a href=""><img src="html/icons/contrat.png" alt="Contrats"> Contrats</a></li>
        <li><a href="/siteSAE2A/reservation"><img src="html/icons/reservation.png" alt="Réservation"> Réservation</a></li>
        <li><a href="siteSAE2A/../utilisateurs"><img src="html/icons/user.png" alt="Utilisateurs"> Utilisateur</a></li>
        <li><a href=""><img src="html/icons/settings.png" alt="Paramètres"> Paramètres</a></li>
        <li><a href="/siteSAE2A/deconnection"><img src="html/icons/logout.png" alt="se déconnecter"> déconnexion</a></li>
    </ul>
</nav>

<div class="top">
    <h1>Utilisateurs</h1>
    <?php if ($isAdmin): ?>
        <a href="#addModal" class="add-btn">+ Ajouter</a>
    <?php endif; ?>
</div>


<header class="topbar">
    <form action="/sitesae2A/utilisateurs" method="get" role="search" class="topbar-form">
        <input type="hidden" name="action" value="rechercherUtilisateur">

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
                    <?= htmlspecialchars($row['id']) ?><br>
                    <?= htmlspecialchars($row['username']) ?><br>
                    <?= htmlspecialchars($row['role']) ?><br>
                    <img src="html/icons/user.png" alt="User" width="100px">
                </p>

                <form method="POST" action ="/sitesae2A/utilisateurs" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                    <input type="hidden" name="action" value="supprimerUtilisateur">
                    <button type="submit"  class="delete-btn">Supprimer</button>

                    
                </form>
                <button type="button" class="delete-btn" onclick="location.hash='editModal-<?= htmlspecialchars($row['id']) ?>'">Modifier</button>
            </div>
            <div id="editModal-<?= htmlspecialchars($row['id']) ?>" class="modal">
                <div class="modal-content">
                    <a href="#" class="close">×</a>
                    <h2>Modifier l'utilisateur</h2>
                    <form method="POST" action="/siteSAE2A/utilisateurs">
                        <input type="hidden" name="action" value="modifierUtilisateur">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                        
                        <label>Nom d'utilisateur<br>
                            <input type="text" name="username" required value="<?= htmlspecialchars($row['username']) ?>">
                        </label><br><br>

                        <label>Rôle<br>
                            <input type="text" name="role" required value="<?= htmlspecialchars($row['role']) ?>">
                        </label><br><br>

                        <button type="submit">Enregistrer</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <div class="rectangle">
            <p>Aucun Utilisateur trouvé</p>
        </div>
    <?php endif; ?>
</div>



<div id="addModal" class="modal">
    <div class="modal-content">
        <a href="" class="close">&times;</a>
        <h2>Ajouter un Utilisateur</h2>
        <form method="POST" action="/siteSAE2A/utilisateurs">
            <input type="hidden" name="action" value="ajouterUtilisateur">
            <input type="text" name="username" placeholder="username" required><br><br>
            <input type="password" name="password" placeholder="password" required><br><br>
            <input type="password" name="confirm" placeholder="confirm" required><br><br>
            <input type="text" name="role" placeholder="role" required><br><br>
            <button type="submit" name="ajouterUtilisateur">Ajouter</button>
            

        </form>
    </div>
</div>


</body>
</html>

