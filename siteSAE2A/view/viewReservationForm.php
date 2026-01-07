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
    <link rel="stylesheet" href="/siteSAE2A/html/css/cars.css">
    <style>
        .page-container { display: flex; gap: 40px; padding: 50px; max-width: 1200px; margin: auto; }
        .devis-section { flex: 1; background: #f9f9f9; padding: 25px; border-radius: 10px; border-left: 6px solid #ff8c00; }
        .form-section { flex: 1.5; }
        .input-field { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>

<div class="page-container">
    <div class="devis-section">
        <h2>Votre Devis</h2>
        <p><strong>Véhicule :</strong> <?php echo htmlspecialchars($numSerie); ?></p>
        <p><strong>Période :</strong> Du <?php echo htmlspecialchars($date_depart); ?> au <?php echo htmlspecialchars($date_retour); ?></p>
        <p><strong>Durée :</strong> <?php echo $nbJours; ?> jour(s)</p>
        <hr>
        <h3 style="color: #ff8c00;">Total à régler : <?php echo $totalTTC; ?> €</h3>
    </div>

    <div class="form-section">
        <h2>Informations de réservation</h2>
        <form action="/siteSAE2A/finaliser_reservation.php" method="POST">
            <input type="hidden" name="num_serie" value="<?php echo $numSerie; ?>">
            <input type="hidden" name="date_debut" value="<?php echo $date_depart; ?>">
            <input type="hidden" name="date_fin" value="<?php echo $date_retour; ?>">

            <label>Nom :</label>
            <input type="text" name="nom" class="input-field" required>

            <label>Email :</label>
            <input type="email" name="email" class="input-field" required>

            <button type="submit" class="btn-orange" style="width: 100%; border: none; cursor: pointer;">
                Confirmer et Payer
            </button>
        </form>
    </div>
</div>

</body>
</html>