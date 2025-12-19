<?php
namespace modele;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



/**
 * Fonction pour envoyer un mail d'alerte
 * @param string $to      Adresse du destinataire
 * @param string $subject Sujet du mail
 * @param string $body    Contenu HTML du mail
 */
function sendAlertEmail($to, $subject, $body) {
    global $smtp_pass;
    $mail = new PHPMailer(true);

    

    try {
        // Configuration du serveur SMTP Gmail
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'rentpark88@gmail.com';           // Ton adresse Gmail
        $mail->Password   = $smtp_pass; // Mot de passe d'application Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Paramètres de l’email
        $mail->setFrom('ton_email@gmail.com', 'Alerte Système');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // Envoi
        $mail->send();
        echo "📧 Mail envoyé avec succès à $to !";
    } catch (Exception $e) {
        echo "❌ Erreur lors de l'envoi : {$mail->ErrorInfo}";
    }
}

// ----------------------
// Exemple d'utilisation
// ----------------------


if ($valeurActuelle > $limite) {
    sendAlertEmail(
        'alban.tixier@hotmail.com',             // à qui envoyer
        'Limite dépassée',                   // sujet
        "<p>Bonjour,</p><p>La valeur actuelle (<b>$valeurActuelle</b>) depasse la limite (<b>$limite</b>).</p>"
    );
}

?> 