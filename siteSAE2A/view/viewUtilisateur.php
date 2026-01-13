<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>RentPark - Utilisateurs</title>
    <script>
    (function () {
        try {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.classList.toggle('dark-theme', theme === 'dark');
        } catch (e) { }
    })();
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/siteSAE2A/html/css/commun.css">
    <link rel="stylesheet" href="/siteSAE2A/html/css/utilisateur.css">
    <link rel="icon" type="image/png" href="html/icons/voiture.png">
</head>
<body>

<nav>
    <ul class="menu">
        <li><a href="/siteSAE2A/home"><i class="fa-solid fa-house"></i> Accueil</a></li>
        <li><a href="/siteSAE2A/dashboard" class="active"><i class="fa-solid fa-chart-line"></i> Tableau de bord</a></li>
        <li><a href="/sitesae2A/voitures"><i class="fa-solid fa-car"></i> Flotte Automobile</a></li>
        <li><a href="/siteSAE2A/planning"><i class="fa-solid fa-file-contract"></i> Contrats</a></li>
        <li><a href="/siteSAE2A/reservation"><i class="fa-solid fa-calendar-check"></i> Réservation</a></li>
        <li><a href="/siteSAE2A/utilisateurs"><i class="fa-solid fa-users-gear"></i> Utilisateurs</a></li>
        <li><a href="/siteSAE2A/parametres"><i class="fa-solid fa-gears"></i> Paramètres</a></li>
        <li class="logout-item"><a href="/siteSAE2A/deconnection"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a></li>
    </ul>
</nav>

<main class="main-content">
    <div class="header-container">
        <h1>Gestion des Utilisateurs</h1>
        <?php if ($role === 'admin') : ?>
            <a href="#addModal" class="add-btn-main">
                <i class="fa-solid fa-user-plus"></i> Ajouter un utilisateur
            </a>
        <?php endif; ?>
    </div>

    <div class="user-container">
        <table class="user-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Rôle</th>
                    <?php if ($role === 'admin') : ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($results)) : ?>
                    <?php foreach ($results as $row) : ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['username']) ?></strong></td>
                            <td><span class="badge-role"><?= htmlspecialchars($row['role']) ?></span></td>
                            <?php if ($role === 'admin') : ?>
                                <td class="actions-cell">
                                    <a href="#editModal-<?= htmlspecialchars($row['username']) ?>" class="btn-icon edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="/siteSAE2A/utilisateurs" onsubmit="return confirm('Supprimer cet utilisateur ?');" style="display:inline;">
                                        <input type="hidden" name="action" value="supprimerUtilisateur">
                                        <input type="hidden" name="username" value="<?= htmlspecialchars($row['username']) ?>">
                                        <button type="submit" class="btn-icon delete"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="3" style="text-align:center; padding: 40px;">Aucun utilisateur trouvé</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php if ($role === 'admin') : ?>
    <div id="addModal" class="modal">
        <div class="modal-content">
            <a href="#" class="close">&times;</a>
            <h2>Nouvel Utilisateur</h2>
            <form method="POST" action="/siteSAE2A/utilisateurs">
                <input type="hidden" name="action" value="ajouterUtilisateur">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Nom d'utilisateur" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Mot de passe" required>
                </div>
                <div class="form-group">
                    <input type="password" name="confirm" placeholder="Confirmer mot de passe" required>
                </div>
                <div class="form-group">
                    <input type="text" name="role" placeholder="Rôle (admin/user)" required>
                </div>
                <button type="submit" class="save-btn">Créer le compte</button>
            </form>
        </div>
    </div>

    <?php foreach ($results as $row) : ?>
        <div id="editModal-<?= htmlspecialchars($row['username']) ?>" class="modal">
            <div class="modal-content">
                <a href="#" class="close">&times;</a>
                <h2>Modifier l'utilisateur</h2>
                <form method="POST" action="/siteSAE2A/utilisateurs">
                    <input type="hidden" name="action" value="modifierUtilisateur">
                    <input type="hidden" name="old_username" value="<?= htmlspecialchars($row['username']) ?>">
                    <div class="form-group">
                        <label>Nom d'utilisateur</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($row['username']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Rôle</label>
                        <input type="text" name="role" value="<?= htmlspecialchars($row['role']) ?>" required>
                    </div>
                    <button type="submit" class="save-btn">Enregistrer</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>