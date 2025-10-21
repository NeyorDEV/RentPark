<?php
if (!isset($role)) {
    if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
    $role = $_SESSION['role'] ?? 'guest';
}
$isAdmin = ($role === 'admin');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>RentPark - Reservation</title>
    <link rel="stylesheet" href="html/css/reservation.css">
    <link rel="icon" type="image/png" href="html/icons/voiture.png">
    <link rel="shortcut icon" href="html/icons/favicon.ico" type="image/x-icon">
</head>
<body>   

<nav>
    <ul class="menu">
        <li><a href=""><img src="html/icons/acceuil.jpg" alt="Accueil"> Accueil</a></li>
        <li><a href="/siteSAE2A/voitures"><img src="html/icons/voiture.png" alt="Flotte"> Flotte Automobile</a></li>
        <li><a href=""><img src="html/icons/contrat.png" alt="Contrats"> Contrats</a></li>
        <li><a href="/siteSAE2A/reservation"><img src="html/icons/reservation.png" alt="Réservations"> Réservations</a></li>
        <li><a href="siteSAE2A/../utilisateurs"><img src="html/icons/user.png" alt="Utilisateurs"> Utilisateur</a></li>
        <li><a href=""><img src="html/icons/settings.png" alt="Paramètres"> Paramètres</a></li>
        
    </ul>       
</nav>

<div class="top">
  <h1>Reservation</h1>
</div>

<header class="topbar">
  <div class="toolbar">
    <form action="/siteSAE2A/reservation" method="get" role="search" class="topbar-form">
      <input type="hidden" name="action" value="rechercherReservation">

      <label for="champ" class="lbl">Rechercher<br>par :</label>
      <?php $champ = $_GET['champ'] ?? 'idContrat'; ?>
      <select id="champ" name="champ">
        <option value="idContrat"   <?= $champ==='idContrat'?'selected':''; ?>>ID</option>
        <option value="Vehicule"    <?= $champ==='Vehicule'?'selected':''; ?>>Véhicule (VIN)</option>
        <option value="Client"      <?= $champ==='Client'?'selected':''; ?>>Client (ID)</option>
      </select>

      <input
        id="q3"
        name="q"
        type="search"
        placeholder="Rechercher..."
        aria-label="Recherche"
        value="<?= htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES); ?>"
      >

      <span class="lbl">Filtrer par :</span>
      <?php $f = $_GET['filtre'] ?? 'en-cours'; ?>
      <select id="filtre" name="filtre">
        <option value="en-cours" <?= $f==='en-cours'?'selected':''; ?>>En cours</option>
        <option value="a-venir"  <?= $f==='a-venir'?'selected':''; ?>>À venir</option>
        <option value="passees"  <?= $f==='passees'?'selected':''; ?>>Passées</option>
        <option value="toutes"   <?= $f==='toutes'?'selected':''; ?>>Toutes</option>
      </select>

      <button type="submit" class="icon-btn" title="Rechercher">🔍</button>
    </form>

    <?php if ($isAdmin): ?>
      <a href="#addReservationModal" class="add-btn">+ Ajouter</a>
    <?php endif; ?>
  </div>
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
                    Reservation n°<?= htmlspecialchars($row['idContrat']) ?><br>
                    Client : <?= htmlspecialchars($row['Client']) ?><br>
                    Vehicule : <?= htmlspecialchars($row['Vehicule']) ?><br>
                    Debut : <?= htmlspecialchars($row['DateDebut']) ?><br>
                    Fin : <?= htmlspecialchars($row['DateFin']) ?><br>
                </p>

        <div class="actions">
          <button type="button" class="edit-btn" onclick="location.hash='editModal-<?= htmlspecialchars($row['idContrat']) ?>'">Modifier</button>

          <form method="POST" action ="/siteSAE2A/reservation" onsubmit="return confirm('Supprimer ce contrat ?');" style="display:inline-block;">
            <input type="hidden" name="action" value="supprimerReservation">
            <input type="hidden" name="id" value="<?= htmlspecialchars($row['idContrat']) ?>">
            <button type="submit"  class="delete-btn">Supprimer</button>
          </form>
        </div>

    
        <div id="editModal-<?= htmlspecialchars($row['idContrat']) ?>" class="modal">
          <div class="modal-content">
            <a href="#" class="close">×</a>
            <h2>Modifier la réservation</h2>
            <form method="POST" action="/siteSAE2A/reservation">
              <input type="hidden" name="action" value="modifierReservation">
              <input type="hidden" name="id" value="<?= htmlspecialchars($row['idContrat']) ?>">

              <label>Véhicule (VIN)<br>
                <input type="text" name="Vehicule" required value="<?= htmlspecialchars($row['Vehicule']) ?>">
              </label><br><br>

              <label>Client (ID)<br>
                <input type="number" name="Client" required value="<?= htmlspecialchars($row['Client']) ?>">
              </label><br><br>

              <label>Début<br>
                <input type="date" name="DateDebut" required value="<?= htmlspecialchars($row['DateDebut']) ?>">
              </label><br><br>

              <label>Fin<br>
                <input type="date" name="DateFin" required value="<?= htmlspecialchars($row['DateFin']) ?>">
              </label><br><br>

              <button type="submit">Enregistrer</button>
            </form>
          </div>
        </div>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <div class="rectangle">
            <p>Aucune réservation trouvée</p>
        </div>
    <?php endif; ?>
</div>


<div id="addReservationModal" class="modal">
  <div class="modal-content">
    <a href="#" class="close">&times;</a>
    <h2>Ajouter une réservation</h2>

    <form method="POST" action="/siteSAE2A/reservation">
      <input type="hidden" name="action" value="ajouterReservation">

      <label for="vehicule">Véhicule (VIN)</label><br>
      <input type="text" id="vehicule" name="Vehicule" placeholder="Ex: 12345678910111213" required><br><br>

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

</body>
</html>