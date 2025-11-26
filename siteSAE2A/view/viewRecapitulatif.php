<?php
// ATTENTION : Ces variables doivent être définies AVANT cette partie du code.
// Elles proviennent généralement de $_SESSION ou de la base de données.

// Exemple des données que votre script devra préparer :
$reservation_data = [
    'modele' => 'Renault Clio V',
    'categorie' => 'Économique',
    'prix_journalier' => 45.00, // Format numérique
    'date_debut' => 'Vendredi 29 Nov. 2025',
    'date_fin' => 'Lundi 02 Déc. 2025',
    'duree_jours' => 3, // Format numérique
    'agence_retrait' => 'Aéroport Lyon Saint-Exupéry',
    'montant_location' => 135.00, // Format numérique (45 * 3)
    'montant_options' => 65.00, // Format numérique
    'montant_total' => 200.00, // Format numérique (135 + 65)
    'options_list' => [
        ['nom' => 'Option GPS', 'prix' => 15.00],
        ['nom' => 'Siège Bébé', 'prix' => 10.00],
        ['nom' => 'Assurance Complète', 'prix' => 40.00],
    ]
];

// Fonction utilitaire pour formater les prix en EUR
function format_prix($prix) {
    return number_format($prix, 2, ',', ' ') . ' €';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Récapitulatif de votre réservation (Vue PHP)</title>
    <link rel="stylesheet" href="html/css/recapitulatif.css">
</head>
<body>

    <div class="recap-container">
        <h1>🚗 Récapitulatif de votre réservation</h1>

        <div class="info-group">
            <h2>Détails du Véhicule</h2>
            <div class="recap-item">
                <span class="label">Modèle :</span>
                <span class="value"><?= htmlspecialchars($reservation_data['modele']) ?></span>
            </div>
            <div class="recap-item">
                <span class="label">Catégorie :</span>
                <span class="value"><?= htmlspecialchars($reservation_data['categorie']) ?></span>
            </div>
            <div class="recap-item">
                <span class="label">Prix journalier de base :</span>
                <span class="value"><?= format_prix($reservation_data['prix_journalier']) ?></span>
            </div>
        </div>

        <div class="info-group">
            <h2>Période et Lieu</h2>
            <div class="recap-item">
                <span class="label">Date de début :</span>
                <span class="value"><?= htmlspecialchars($reservation_data['date_debut']) ?></span>
            </div>
            <div class="recap-item">
                <span class="label">Date de fin :</span>
                <span class="value"><?= htmlspecialchars($reservation_data['date_fin']) ?></span>
            </div>
            <div class="recap-item">
                <span class="label">Durée totale :</span>
                <span class="value"><?= htmlspecialchars($reservation_data['duree_jours']) ?> jour(s)</span>
            </div>
            <div class="recap-item">
                <span class="label">Agence de retrait :</span>
                <span class="value"><?= htmlspecialchars($reservation_data['agence_retrait']) ?></span>
            </div>
        </div>
        
        <div class="info-group">
            <h2>Détail des Coûts</h2>
            <div class="recap-item">
                <span class="label">Location (<?= htmlspecialchars($reservation_data['duree_jours']) ?> jours x <?= format_prix($reservation_data['prix_journalier']) ?>) :</span>
                <span class="value"><?= format_prix($reservation_data['montant_location']) ?></span>
            </div>

            <?php foreach ($reservation_data['options_list'] as $option): ?>
            <div class="recap-item" style="padding-left: 15px; font-style: italic;">
                <span class="label"><?= htmlspecialchars($option['nom']) ?> :</span>
                <span class="value">+ <?= format_prix($option['prix']) ?></span>
            </div>
            <?php endforeach; ?>
            <div class="recap-item">
                <span class="label" style="font-weight: bold;">Total des options :</span>
                <span class="value" style="font-weight: bold;"><?= format_prix($reservation_data['montant_options']) ?></span>
            </div>
        </div>

        <div class="recap-total">
            <span class="total-label">Montant Total à Payer :</span>
            <span class="total-value"><?= format_prix($reservation_data['montant_total']) ?></span>
        </div>

        <div class="actions">
            <form action="traitement_paiement.php" method="POST" style="display: inline;">
                <input type="hidden" name="action" value="confirmer_reservation">
                <button type="submit" class="btn btn-confirm">Confirmer et Passer au Paiement</button>
            </form>
            
            <a href="selection_options.php" class="btn btn-modify">Modifier la Réservation</a>
        </div>

    </div>

</body>
</html>