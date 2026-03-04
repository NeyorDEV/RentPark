<!DOCTYPE html>
<html lang="fr">
<head>
    <?php
        $url= $_SERVER['REQUEST_URI'];
        $args= explode('/', $url);
        $client = $args[2]==="clients";
    ?>
    
    <meta charset="utf-8">
    <?php if($client): ?>
        <title>RentPark - Clients</title>
    <?php else: ?>
        <title>RentPark - Utilisateurs</title>
    <?php endif; ?>
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
    <link rel="shortcut icon" href="html/icons/favicon.ico" type="image/x-icon">
</head>
<body>

<nav>
    <ul class="menu">
        <li><a href="/siteSAE2A/home"><i class="fa-solid fa-house"></i> Accueil</a></li>
        <?php if ($role === 'admin') : ?>
            <li><a href="/siteSAE2A/dashboard" class="active"><i class="fa-solid fa-chart-line"></i> Tableau de bord</a></li>
        <?php endif; ?>
        <li><a href="/sitesae2A/voitures"><i class="fa-solid fa-car"></i> Flotte Automobile</a></li>
        <li><a href="/siteSAE2A/planning"><i class="fa-solid fa-file-contract"></i> Planning</a></li>
        <li><a href="/siteSAE2A/reservation"><i class="fa-solid fa-calendar-check"></i> Contrats</a></li>
        <li><a href="/siteSAE2A/utilisateurs"><i class="fa-solid fa-users-gear"></i> Utilisateurs</a></li>
        <li><a href="/siteSAE2A/parametres"><i class="fa-solid fa-gears"></i> Paramètres</a></li>
        <li class="logout-item"><a href="/siteSAE2A/deconnection"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a></li>
    </ul>
</nav>

<main class="main-content">
    <div class="header-container">
        
        <?php if($client): ?>
            <div class="left flex-column">
                <a href="/siteSAE2A/utilisateurs" class="add-btn-main btn-left unselected">
                    <i class="fa-solid fa-exchange-alt"></i> Utilisateurs
                </a>
                <div style="height: 10px;"></div>
                <a  class="add-btn-main btn-left selected">
                     Clients
                </a>
            </div>
            <h1>Gestion des Clients</h1>
            <?php if ($role === 'admin') : ?>
                <a href="#addModalClient" class="add-btn-main btn-right unselected">
                    <i class="fa-solid fa-user-plus"></i> Ajouter un Client
                </a>
            <?php endif; ?>
        <?php else: ?>
            <div class="left flex-column">
                <a  class="add-btn-main btn-left selected">
                    <i class="fa-solid"></i> Utilisateurs
                </a>
                <div style="height: 10px;"></div>
                <a href="/siteSAE2A/clients" class="add-btn-main btn-left unselected">
                    <i class="fa-solid fa-exchange-alt"></i> Clients
                </a>
            </div>
            <h1>Gestion des Utilisateurs</h1>
            <?php if ($role === 'admin') : ?>
                <a href="#addModalUtilisateur" class="add-btn-main btn-right unselected">
                    <i class="fa-solid fa-user-plus"></i> Ajouter un utilisateur
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="user-container">
        <table class="user-table">
            <thead>
                <tr>
                    
                    <?php if($client): ?>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Numéro Téléphone</th>
                    <?php else: ?>
                        <th>Nom d'Utilisateur</th>
                        <th>Rôle</th>
                    <?php endif; ?>
                    <?php if ($role === 'admin') : ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="10" style="background-color: transparent; padding: 1px;"></td></tr>
                <?php if ($client) : ?>
                    <?php if (!empty($results)) : ?>
                        <?php foreach ($results as $row) : ?>
                            <tr class="person-box clickable-row" data-target="Show-Modal-Client-<?= $row['IdClient'] ?>">
                                <td>
                                    <strong><?= htmlspecialchars($row['Nom']) ?></strong>
                                </td>
                                <td><strong><?= htmlspecialchars($row['Prenom']) ?></strong></td>
                                <td><span class="badge-role"><?= htmlspecialchars($row['NumTel']) ?></span></td>
                                <?php if ($role === 'admin') : ?>
                                
                                    <td >
                                        <a href="#editModalClient-<?= htmlspecialchars($row['IdClient']) ?>" class="btn-icon edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="3" style="text-align:center; padding: 40px;">Aucun Client trouvé</td></tr>
                    <?php endif; ?>

                <?php else : ?>
                    <?php if (!empty($results)) : ?>
                        <?php foreach ($results as $row) : ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($row['username']) ?></strong></td>
                                <td><span class="badge-role"><?= htmlspecialchars($row['role']) ?></span></td>
                                <?php if ($role === 'admin') : ?>
                                    <td class="actions-cell">
                                        <a href="#editModalUtilisateur-<?= htmlspecialchars($row['username']) ?>" class="btn-icon edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <form method="POST" action="/sitesae2A/utilisateurs" onsubmit="return confirm('Supprimer cet utilisateur ?');" style="display:inline;">
                                            <input type="hidden" name="action" value="supprimerUtilisateur">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                            <button type="submit" class="btn-icon delete"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="3" style="text-align:center; padding: 40px;">Aucun utilisateur trouvé</td></tr>
                    <?php endif; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>


<?php if ($client):?>
    <?php foreach ($results as $row) : ?>
        <div id="Show-Modal-Client-<?= htmlspecialchars($row['IdClient']) ?>" class="modal">
            <div class="modal-content">
                <a href="#" class="close">&times;</a>
                <h2>Client N°<?= htmlspecialchars($row['IdClient']) ?></h2>
                <div class="client-details">
                    <h3>Nom <br>
                        <p><?= htmlspecialchars($row['Nom']) ?></p>
                    </h3><br>
                    <h3>Prenom <br>
                        <p><?= htmlspecialchars($row['Prenom']) ?></p>
                    </h3><br>
                    <h3>Email <br>
                        <p><?= htmlspecialchars($row['Email']) ?></p>
                    </h3><br>
                    <h3>Numéro de téléphone <br>
                        <p><?= htmlspecialchars($row['NumTel']) ?></p>
                    </h3><br>
                    <h3>Numéro de permis <br>
                        <p><?= htmlspecialchars($row['NumPermis']) ?></p>
                    </h3><br>
                    <h3>Date de naissance <br>
                        <p><?= htmlspecialchars($row['DateNaiss']) ?></p>
                    </h3><br>
                    <h3>Nationalité <br>
                        <p><?= htmlspecialchars($row['Nationalite']) ?></p>
                    </h3><br>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if ($role === 'admin') : ?>
    <div id="addModalClient" class="modal">
        <div class="modal-content">
            <a href="" class="close">&times;</a>
            <h2>Ajouter un Client</h2>
            <form method="POST" action="/siteSAE2A/clients">
                <input type="hidden" name="action" value="ajouterClient">
                    <div class="form-group">
                        <input type="text" name="nom" placeholder="Nom du client" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="prenom" placeholder="Prénom du client" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email du client" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="numTel" placeholder="Numéro de téléphone du client" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="numPermis" placeholder="Numéro de permis du client" required>
                    </div>
                    <div class="form-group">
                        <input type="date" name="dateNaiss" placeholder="Date de naissance du client" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="nationalite" placeholder="Nationalité du client" required>
                    </div>
                    <button class="form-button" type="submit" >Créer le Client</button>
            </form>
        </div>
    </div>
    <?php foreach ($results as $row) : ?>
        <div id="editModalClient-<?= htmlspecialchars($row['IdClient']) ?>" class="modal">
            <div class="modal-content">
                <a href="#" class="close">&times;</a>
                <h2>Modifier le Client</h2>
                <form method="POST" action="/siteSAE2A/clients">
                    <input type="hidden" name="action" value="modifierClient">
                    <input type="hidden" name="idClient" value="<?= htmlspecialchars($row['IdClient']) ?>">
                    <label>Nom <br>
                    <div class="form-group">
                        <input type="text" name="nom" required value="<?= htmlspecialchars($row['Nom']) ?>">
                    </div>
                    </label>
                    <label>Prenom <br>
                    <div class="form-group">
                        <input type="text" name="prenom" required value="<?= htmlspecialchars($row['Prenom']) ?>">
                    </div>
                    </label>
                    <label>Email <br>
                    <div class="form-group">
                        <input type="email" name="email" required value="<?= htmlspecialchars($row['Email']) ?>">
                    </div>
                    </label>
                    <label>Numéro de téléphone <br> 
                    <div class="form-group">
                        <input type="text" name="numTel" required value="<?= htmlspecialchars($row['NumTel']) ?>">
                    </div>
                    </label>
                    <label>Numéro de permis <br>
                    <div class="form-group">
                        <input type="text" name="numPermis" required value="<?= htmlspecialchars($row['NumPermis']) ?>">
                    </div>
                    </label>
                    <label>Date de naissance <br>
                    <div class="form-group">
                        <input type="date" name="dateNaiss" required value="<?= htmlspecialchars($row['DateNaiss']) ?>">
                    </div>
                    </label>
                    <label>Nationalité <br>
                    <div class="form-group">
                        <input type="text" name="nationalite" required value="<?= htmlspecialchars($row['Nationalite']) ?>">
                    </div>
                    </label>
                    <button class="form-button" type="submit">Enregistrer</button>

                </form>
            </div>
        </div>
    <?php endforeach; ?>
    
    
<?php endif; ?>


<?php else: ?>
    <?php if ($role === 'admin') : ?>
    <div id="addModalUtilisateur" class="modal">
    <div class="modal-content">
        <a href="" class="close">&times;</a>
        <h2>Ajouter un Utilisateur</h2>
        <form method="POST" action="/siteSAE2A/utilisateurs">
            <input type="hidden" name="action" value="ajouterUtilisateur">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Nom d'utilisateur" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Mot de passe" required>
                </div>
                <label>Rôle<br>
                    <div class="form-group">
                        <select name="role" required>
                            <option value="" selected disabled hidden>Choisir le rôle</option>
                            <option value="user" >
                                Utilisateur
                            </option>
                            <option value="employe" >
                                Employé
                            </option>
                            <option value="admin" >
                                Admin
                            </option>
                        </select>
                    </div>
                </label>
                <button type="submit" class="form-button">Créer le compte</button>
        </form>
    </div>
</div>

    <?php foreach ($results as $row) : ?>
        <div id="editModalUtilisateur-<?= htmlspecialchars($row['username']) ?>" class="modal">
            <div class="modal-content">
                <a href="#" class="close">&times;</a>
                <h2>Modifier l'utilisateur</h2>
                <form method="POST" action="/siteSAE2A/utilisateurs">
                    <input type="hidden" name="action" value="modifierUtilisateur">
                    <input type="hidden" name="old_username" value="<?= htmlspecialchars($row['username']) ?>">
                   <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                        
                        <label>Nom d'utilisateur<br>
                            <div class="form-group">
                                <input type="text" name="username" required value="<?= htmlspecialchars($row['username']) ?>">
                            </div>
                        </label>
                        <label>Rôle<br>
                            <div class="form-group">
                                <select name="role" required>
                                    <option value="user" <?= ($row['role'] === 'user') ? 'selected' : '' ?>>
                                        Utilisateur
                                    </option>
                                    <option value="employe" <?= ($row['role'] === 'employe') ? 'selected' : '' ?>>
                                        Employé
                                    </option>
                                    <option value="admin" <?= ($row['role'] === 'admin') ? 'selected' : '' ?>>
                                        Admin
                                    </option>
                                </select>
                            </div>
                        </label>
                        <button class="form-button" type="submit">Enregistrer</button>
                </form>
            </div>
            
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<?php endif; ?>


<script>
document.querySelectorAll('.clickable-row').forEach(row => {
    row.addEventListener('click', function(e) {

        // Empêche d’ouvrir le modal si on clique sur un bouton ou lien
        if (e.target.closest('a, button, form')) return;

        const modalId = this.dataset.target;
        window.location.hash = modalId;
    });
});
</script>

</body>
</html>