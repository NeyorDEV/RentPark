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

// Note : Idéalement, récupérez le prix via votre API
$prixJournalier = 100; 
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
        <p><strong>Véhicule (N° Série) :</strong> <span><?php echo htmlspecialchars($numSerie); ?></span></p>
        <p><strong>Période :</strong> <span>Du <?php echo htmlspecialchars($date_depart); ?> au <?php echo htmlspecialchars($date_retour); ?></span></p>
        <p><strong>Durée :</strong> <span><?php echo $nbJours; ?> jour(s)</span></p>
        <hr>
        <div class="total-container">
            <h3>Total à régler :</h3>
            <span class="price-tag"><?php echo $totalTTC; ?> €</span>
        </div>
    </div>

    <div class="form-section">
        <h2>Informations de réservation</h2>
        <form action="/siteSAE2A/recapitulatif" method="POST">
            <input type="hidden" name="num_serie" value="<?php echo htmlspecialchars($numSerie); ?>">
            <input type="hidden" name="date_debut" value="<?php echo htmlspecialchars($date_depart); ?>">
            <input type="hidden" name="date_fin" value="<?php echo htmlspecialchars($date_retour); ?>">
            <input type="hidden" name="prix_total" value="<?php echo $totalTTC; ?>">

            <div class="form-row">
                <div class="form-group">
                    <label>Nom :</label>
                    <input type="text" name="nom" class="input-field" placeholder="Ex: Dupont" required>
                </div>
                <div class="form-group">
                    <label>Prénom :</label>
                    <input type="text" name="prenom" class="input-field" placeholder="Ex: Jean" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Date de naissance :</label>
                    <input type="date" name="datenaiss" class="input-field" required>
                </div>
                <div class="form-group">
                    <label>Nationalité :</label>
                    <input type="text" name="nationalite" class="input-field" placeholder="Ex: Française" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Numéro de téléphone :</label>
                    <input type="tel" name="numTel" class="input-field" placeholder="06 00 00 00 00" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email :</label>
                    <input type="email" name="email" class="input-field" placeholder="jean.dupont@exemple.com" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Numéro de permis de conduire :</label>
                    <input type="text" name="numPermis" class="input-field" placeholder="Ex: 15AA00000" required>
                </div>
            </div>

            <button type="submit" class="btn-orange">
                Confirmer la réservation
            </button>
        </form>
    </div>
</div>

</body>
</html>