<?php
// 1. Récupération des données passées dans l'URL
$numSerie = $_GET['id'] ?? null;
$date_depart = $_GET['date_depart'] ?? '';
$date_retour = $_GET['date_retour'] ?? '';

// 2. Calcul du nombre de jours pour le devis
$d1 = new DateTime($date_depart);
$d2 = new DateTime($date_retour);
$interval = $d1->diff($d2);
$nbJours = $interval->days > 0 ? $interval->days : 1; 

// Note : Idéalement, récupérez le prix via votre API pour afficher le montant total ici
$prixJournalier = 100; // Exemple statique à remplacer
$totalTTC = $nbJours * $prixJournalier;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votre Devis - Rentpark</title>
    <link rel="stylesheet" href="/siteSAE2A/html/css/reservationForm.css">
</head>
<body>

<div class="page-container">
    <div class="devis-section">
        <h2>Votre Devis</h2>
        <p><strong>Véhicule (N° Série) :</strong> <?php echo htmlspecialchars($numSerie); ?></p>
        <p><strong>Période :</strong> Du <?php echo htmlspecialchars($date_depart); ?> au <?php echo htmlspecialchars($date_retour); ?></p>
        <p><strong>Durée :</strong> <?php echo $nbJours; ?> jour(s)</p>
        <hr>
        <h3 style="color: #ff8c00;">Total à régler : <?php echo $totalTTC; ?> €</h3>
    </div>

    <div class="form-section">
        <h2>Informations de réservation</h2>
        <form action="/siteSAE2A/finaliser_reservation.php" method="POST">
            <input type="hidden" name="num_serie" value="<?php echo htmlspecialchars($numSerie); ?>">
            <input type="hidden" name="date_debut" value="<?php echo htmlspecialchars($date_depart); ?>">
            <input type="hidden" name="date_fin" value="<?php echo htmlspecialchars($date_retour); ?>">
            <input type="hidden" name="prix_total" value="<?php echo $totalTTC; ?>">

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1;">
                    <label>Nom :</label>
                    <input type="text" name="nom" class="input-field" placeholder="Ex: Dupont" required>
                </div>
                <div style="flex: 1;">
                    <label>Prénom :</label>
                    <input type="text" name="prenom" class="input-field" placeholder="Ex: Jean" required>
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1;">
                    <label>Date de naissance :</label>
                    <input type="date" name="datenaiss" class="input-field" required>
                </div>
                <div style="flex: 1;">
                    <label>Nationalité :</label>
                    <input type="text" name="nationalite" class="input-field" placeholder="Ex: Française" required>
                </div>
            </div>

            <label>Numéro de téléphone :</label>
            <input type="tel" name="numTel" class="input-field" placeholder="06 00 00 00 00" required>

            <label>Email :</label>
            <input type="email" name="email" class="input-field" placeholder="jean.dupont@exemple.com" required>

            <label>Numéro de permis de conduire :</label>
            <input type="text" name="numPermis" class="input-field" placeholder="Ex: 15AA00000" required>

            <button type="submit" class="btn-orange" style="width: 100%; border: none; cursor: pointer; margin-top: 20px; font-weight: bold;">
                Confirmer la réservation
            </button>
        </form>
    </div>
</div>

</body>
</html>