<?php
if (!isset($role)) {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $role = $role ?? 'guest';
}
$isAdmin = ($role === 'admin');
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>RentPark - Reservation</title>
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
    <link rel="stylesheet" href="/siteSAE2A/html/css/commun.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="html/css/reservation.css">
    <link rel="icon" type="image/png" href="html/icons/voiture.png">
    <link rel="shortcut icon" href="html/icons/favicon.ico" type="image/x-icon">
</head>

<body>

<nav>
    <ul class="menu">
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
            <li class="logout-item"><a href="/siteSAE2A/deconnection"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a></li>
        <?php endif;?>
    </ul>
</nav>

    <div class="main-content">
        <h1>Contrats</h1>

        <header class="topbar">
            <div class="toolbar">
                <form action="/siteSAE2A/reservation" method="get" role="search" class="topbar-form">
                    <input type="hidden" name="action" value="rechercherReservation">

                    <label for="champ" class="lbl">Rechercher<br>par :</label>
                    <?php $champ = $_GET['champ'] ?? 'idContrat'; ?>
                    <select id="champ" name="champ">
                        <option value="idContrat" <?= $champ === 'idContrat' ? 'selected' : ''; ?>>ID</option>
                        <option value="Vehicule" <?= $champ === 'Vehicule' ? 'selected' : ''; ?>>Véhicule (VIN)</option>
                        <option value="Client" <?= $champ === 'Client' ? 'selected' : ''; ?>>Client (ID)</option>
                    </select>

                    <input id="q3" name="q" type="search" placeholder="Rechercher..." aria-label="Recherche"
                        value="<?= htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES); ?>">

                    <span class="lbl">Filtrer par :</span>
                    <?php $f = $_GET['filtre'] ?? 'en-cours'; ?>
                    <select id="filtre" name="filtre">
                        <option value="en-cours" <?= $f === 'en-cours' ? 'selected' : ''; ?>>En cours</option>
                        <option value="a-venir" <?= $f === 'a-venir' ? 'selected' : ''; ?>>À venir</option>
                        <option value="passees" <?= $f === 'passees' ? 'selected' : ''; ?>>Passées</option>
                        <option value="a-valider" <?= $f === 'a-valider' ? 'selected' : ''; ?>>À valider</option>
                        <option value="toutes" <?= $f === 'toutes' ? 'selected' : ''; ?>>Toutes</option>
                    </select>

                    <button type="submit" class="icon-btn" title="Rechercher">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>

                <?php if ($isAdmin): ?>
                    <a href="#addReservationModal" class="add-btn">+ Ajouter</a>
                <?php endif; ?>
            </div>
        </header>

        <?php if (!empty($dVueErreur)): ?>
            <div class="erreurs">
                <ul>
                    <?php foreach ($dVueErreur as $erreur): ?>
                        <li><?= htmlspecialchars($erreur) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="reservation">
            <?php if (!empty($results)): ?>
                <?php foreach ($results as $row): ?>
                    <div class="rectangle">
                        <p>
                            <b>Reservation n°<?= htmlspecialchars($row['idContrat']) ?></b><br>
                            Statut : <b><?= htmlspecialchars($row['Statut'] ?? 'Inconnu') ?></b><br>
                            Client : <?= htmlspecialchars($row['IdClient']) ?><br>
                            Vehicule : <?= htmlspecialchars($row['idVehicule']) ?><br>
                            Debut : <?= htmlspecialchars($row['DateDebut']) ?><br>
                            Fin : <?= htmlspecialchars($row['DateFin']) ?><br>
                        </p>

                        <div class="actions">
                            <?php if (isset($row['Statut']) && $row['Statut'] === 'EnCoursValidation'): ?>
                                <form method="POST" action="/siteSAE2A/reservation"
                                    onsubmit="openConfirmModal(event, 'Voulez-vous vraiment accepter cette réservation ?');"
                                    style="display:inline-block;">
                                    <input type="hidden" name="action" value="changerStatut">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($row['idContrat']) ?>">
                                    <input type="hidden" name="nouveauStatut" value="Validé">
                                    <button type="submit" class="accept-btn">Accepter</button>
                                </form>

                                <form method="POST" action="/siteSAE2A/reservation"
                                    onsubmit="openConfirmModal(event, 'Voulez-vous vraiment refuser cette réservation ?');"
                                    style="display:inline-block;">
                                    <input type="hidden" name="action" value="changerStatut">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($row['idContrat']) ?>">
                                    <input type="hidden" name="nouveauStatut" value="Annulé">
                                    <button type="submit" class="delete-btn">Refuser</button>
                                </form>

                            <?php else: ?>
                                <button type="button" class="edit-btn"
                                    onclick="location.hash='editModal-<?= htmlspecialchars($row['idContrat']) ?>'">Modifier</button>

                                <form method="POST" action="/siteSAE2A/reservation"
                                    onsubmit="openConfirmModal(event, 'Voulez-vous vraiment supprimer ce contrat ? Toute suppression est définitive.');"
                                    style="display:inline-block;">
                                    <input type="hidden" name="action" value="supprimerReservation">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($row['idContrat']) ?>">
                                    <button type="submit" class="delete-btn">Supprimer</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="rectangle">
                    <p>Aucune réservation trouvée</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($results)): ?>
            <?php foreach ($results as $row): ?>
                <div id="editModal-<?= htmlspecialchars($row['idContrat']) ?>" class="modal">
                    <div class="modal-content">
                        <a href="#" class="close">×</a>
                        <h2>Modifier la réservation</h2>
                        <form method="POST" action="/siteSAE2A/reservation">
                            <input type="hidden" name="action" value="modifierReservation">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($row['idContrat']) ?>">

                            <label>Véhicule (VIN)<br>
                                <input type="text" name="Vehicule" required value="<?= htmlspecialchars($row['idVehicule']) ?>">
                            </label><br><br>

                            <label>Client (ID)<br>
                                <input type="number" name="Client" required value="<?= htmlspecialchars($row['IdClient']) ?>">
                            </label><br><br>

                            <div style="display: flex; gap: 10px;">
                                <div style="flex: 1;">
                                    <label>Début<br>
                                        <input type="date" name="DateDebut" required
                                            value="<?= htmlspecialchars($row['DateDebut']) ?>">
                                    </label>
                                </div>
                                <div style="flex: 1;">
                                    <label>Fin<br>
                                        <input type="date" name="DateFin" required
                                            value="<?= htmlspecialchars($row['DateFin']) ?>">
                                    </label>
                                </div>
                            </div><br>

                            <button type="submit">Enregistrer les modifications</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>




        <div id="addReservationModal" class="modal">
            <div class="modal-content">
                <a href="#" class="close">&times;</a>
                <h2>Ajouter une réservation</h2>

                <form method="POST" action="/siteSAE2A/reservation">
                    <input type="hidden" name="action" value="ajouterReservation">

                    <label for="vehicule">Véhicule (VIN)</label><br>
                    <input type="text" id="vehicule" name="Vehicule" placeholder="Ex: 12345678910111213"
                        required><br><br>

                    <label for="client">Client (ID)</label><br>
                    <input type="number" id="client" name="Client" placeholder="Ex: 1" required><br><br>

                    <label for="dateDebut">Début</label><br>
                    <input type="date" id="dateDebut" name="DateDebut" required><br><br>

                    <label for="dateFin">Fin</label><br>
                    <input type="date" id="dateFin" name="DateFin" required><br><br>

                    <button type="submit">Créer la réservation</button>
                </form>

            </div>
        </div>
    </div>

    <div id="customConfirmModal" class="modal">
        <div class="modal-content" style="text-align: center; max-width: 400px;">
            <h2>Confirmation</h2>
            <p id="confirmMessage" style="margin: 20px 0; font-size: 16px;">Êtes-vous sûr ?</p>

            <div style="display: flex; justify-content: center; gap: 15px; margin-top: 20px;">
                <button type="button" id="btnCancelAction" class="delete-btn">Annuler</button>
                <button type="button" id="btnConfirmAction" class="accept-btn">Confirmer</button>
            </div>
        </div>
    </div>

    <script>
        let formToSubmit = null;

        function openConfirmModal(event, message) {
            event.preventDefault();
            formToSubmit = event.target;

            document.getElementById('confirmMessage').innerText = message;
            document.getElementById('customConfirmModal').style.display = 'flex';
        }

        function closeConfirmModal() {
            document.getElementById('customConfirmModal').style.display = 'none';
            formToSubmit = null;
        }

        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById('btnCancelAction').addEventListener('click', closeConfirmModal);

            document.getElementById('btnConfirmAction').addEventListener('click', function () {
                if (formToSubmit) {
                    formToSubmit.submit();
                }
            });
        });
    </script>

</body>

</html>