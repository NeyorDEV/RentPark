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
        <li><a href="index.php"><img src="html/icons/voiture.png" alt="Flotte"> Flotte Automobile</a></li>
        <li><a href=""><img src="html/icons/contrat.png" alt="Contrats"> Contrats</a></li>
        <li><a href="index.php?action=listeReservation"><img src="html/icons/reservation.png" alt="Réservations"> Réservations</a></li>
        <li><a href=""><img src="html/icons/settings.png" alt="Paramètres"> Paramètres</a></li>
        
    </ul>       
</nav>

<div class="top">
  <h1>Reservation</h1>
</div>

<header class="topbar">
  <div class="toolbar">
    <form action="index.php" method="get" role="search" class="topbar-form">
      <input type="hidden" name="action" value="rechercherReservation">

      <label for="champ" class="lbl">Rechercher<br>par :</label>
      <?php $champ = $_GET['champ'] ?? 'idContrat'; ?>
      <select id="champ" name="champ">
        <option value="idContrat"   <?= $champ==='idContrat'?'selected':''; ?>>ID</option>
        <option value="Vehicule"    <?= $champ==='Vehicule'?'selected':''; ?>>Véhicule (VIN)</option>
        <option value="Client"      <?= $champ==='Client'?'selected':''; ?>>Client (ID)</option>
        <option value="DateDebut"   <?= $champ==='DateDebut'?'selected':''; ?>>Date Debut</option>
        <option value="DateFin"     <?= $champ==='DateFin'?'selected':''; ?>>Date Fin</option>
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
      <a href="#addContratModal" class="add-btn">+ Ajouter</a>
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
                    Vehicule : <?= htmlspecialchars($row['Vehicule']) ?><br>
                    Debut : <?= htmlspecialchars($row['DateDebut']) ?><br>
                    Fin : <?= htmlspecialchars($row['DateFin']) ?><br>
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


<div id="addContratModal" class="modal">
  <div class="modal-content">
    <a href="#" class="close">&times;</a>
    <h2>Ajouter un contrat</h2>

    <form method="POST" action="index.php">
      <input type="hidden" name="action" value="ajouterContrat">

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