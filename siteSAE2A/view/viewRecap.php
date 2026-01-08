<?php
// Récupération des données du client et de la voiture
$nom = $_POST['nom'] ?? 'N/C';
$prenom = $_POST['prenom'] ?? 'N/C';
$email = $_POST['email'] ?? 'N/C';
$numTel = $_POST['numTel'] ?? 'N/C';
$numPermis = $_POST['numPermis'] ?? 'N/C';
$numSerie = $_POST['num_serie'] ?? 'N/C';
$date_debut = $_POST['date_debut'] ?? '';
$date_fin = $_POST['date_fin'] ?? '';
$prix_total = $_POST['prix_total'] ?? 0;

// Calculs financiers
$tva = $prix_total * 0.20;
$ht = $prix_total - $tva;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Récapitulatif - Devis Rentpark</title>
    <link rel="stylesheet" href="/siteSAE2A/html/css/recap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="invoice-container">
    <header class="invoice-header">
        <div class="brand">
            <h1 class="logo-font">RENTPARK<span>.</span></h1>
            <p>Location de véhicules d'exception</p>
        </div>
        <div class="status-badge">DEVIS À RÉGLER EN AGENCE</div>
    </header>

    <div class="invoice-body">
        <div class="info-grid">
            <div class="info-block">
                <h3><i class="fa-solid fa-user"></i> Informations Locataire</h3>
                <p><strong><?php echo htmlspecialchars($prenom . ' ' . $nom); ?></strong></p>
                <p><?php echo htmlspecialchars($email); ?></p>
                <p><?php echo htmlspecialchars($numTel); ?></p>
                <p>N° Permis : <?php echo htmlspecialchars($numPermis); ?></p>
            </div>

            <div class="info-block">
                <h3><i class="fa-solid fa-car"></i> Détails Réservation</h3>
                <p>Véhicule : <strong><?php echo htmlspecialchars($numSerie); ?></strong></p>
                <p>Du : <?php echo htmlspecialchars($date_debut); ?></p>
                <p>Au : <?php echo htmlspecialchars($date_fin); ?></p>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Qté</th>
                    <th>Prix Unitaire</th>
                    <th>Total HT</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Location de véhicule (Réf: <?php echo htmlspecialchars($numSerie); ?>)<br>
                    <small>Assurance standard incluse</small></td>
                    <td>1</td>
                    <td><?php echo number_format($ht, 2); ?> €</td>
                    <td><?php echo number_format($ht, 2); ?> €</td>
                </tr>
            </tbody>
        </table>

        <div class="invoice-footer">
            <div class="payment-method">
                <p class="payment-title"><strong>Modes de règlement acceptés :</strong></p>
                <ul class="payment-list">
                    <li><i class="fa-solid fa-money-bill-wave"></i> Espèces (Liquide)</li>
                    <li><i class="fa-solid fa-money-check-dollar"></i> Chèque</li>
                    <li><i class="fa-brands fa-cc-visa"></i> Carte Bancaire</li>
                </ul>
                
                <div class="agency-notice">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>
                        <strong>Paiement en agence uniquement</strong><br>
                        Le règlement s'effectue lors du retrait du véhicule. Aucun débit n'est effectué en ligne.
                    </span>
                </div>
            </div>

            <div class="totals">
                <div class="total-row">
                    <span>Montant HT :</span>
                    <span><?php echo number_format($ht, 2); ?> €</span>
                </div>
                <div class="total-row">
                    <span>TVA (20%) :</span>
                    <span><?php echo number_format($tva, 2); ?> €</span>
                </div>
                <div class="total-row grand-total">
                    <span>Total TTC :</span>
                    <span><?php echo number_format($prix_total, 2); ?> €</span>
                </div>
            </div>
        </div>
    </div>

    <div class="actions-bar">
    <button onclick="window.print()" class="btn-secondary">
        <i class="fa-solid fa-print"></i> Imprimer le devis
    </button>

    <form action="/siteSAE2A/finaliserReservation" method="POST">
    <input type="hidden" name="nom" value="<?php echo htmlspecialchars($_POST['nom']); ?>">
    <input type="hidden" name="prenom" value="<?php echo htmlspecialchars($_POST['prenom']); ?>">
    <input type="hidden" name="email" value="<?php echo htmlspecialchars($_POST['email']); ?>">
    <input type="hidden" name="numTel" value="<?php echo htmlspecialchars($_POST['numTel']); ?>">
    <input type="hidden" name="numPermis" value="<?php echo htmlspecialchars($_POST['numPermis']); ?>">
    <input type="hidden" name="datenaiss" value="<?php echo htmlspecialchars($_POST['datenaiss']); ?>">
    <input type="hidden" name="nationalite" value="<?php echo htmlspecialchars($_POST['nationalite']); ?>">

    <input type="hidden" name="num_serie" value="<?php echo htmlspecialchars($_POST['num_serie']); ?>">
    <input type="hidden" name="date_debut" value="<?php echo htmlspecialchars($_POST['date_debut']); ?>">
    <input type="hidden" name="date_fin" value="<?php echo htmlspecialchars($_POST['date_fin']); ?>">
    <input type="hidden" name="prix_total" value="<?php echo htmlspecialchars($_POST['prix_total']); ?>">

    <button type="submit" class="btn-orange">CONFIRMER LA RÉSERVATION</button>
</form>
</div>

</body>
</html>