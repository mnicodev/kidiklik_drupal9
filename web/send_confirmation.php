<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '_vendor/autoload.php';
//header('Content-Type: application/json');

$mail = new PHPMailer(true);

try {
    $email = $_GET['email'] ?? $_POST['email'];
    if(empty($email)) {
        header('location: /');
    }
    $token = $_GET['token'] ?? $_POST['token'];
    if(empty($token)) {
        header('location: /');
    }
    $url_confirmation = base64_decode($token);
    $parts = parse_url($url_confirmation);
    parse_str($parts['query'], $params);
    $token = $params['token'];
    if(empty($token)) {
        header('location: /');
    }
    // Server settings
    $mail->isSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->Host       = 'smtp-web.2isr.local';   // ton serveur SMTP
    $mail->SMTPAuth   = false;                    // true si authentification
    $mail->Port = 25;
    $mail->SMTPSecure = false;
    $mail->SMTPAutoTLS = false;
    // Destinataires
    $mail->setFrom('contact@kidiklik.fr', 'Contact kidiklik');
    $mail->addAddress($email, 'Destinataire');

    // Contenu
    $mail->isHTML(true);
    $mail->Subject = htmlspecialchars('Super, vous êtes presque inscrit !', ENT_QUOTES, 'UTF-8');
    $mail->Body = "Bienvenue chez Kidiklik!<br><a href='".$url_confirmation."'>Veuillez cliquez sur ce lien pour confirmer votre inscription</a>.<br>Merci de nous accorder votre confiance pour pimenter vos sorties avec les enfants.<br><br>L'équipe Kidiklik.";

    $mail->send();
    header('location: /waiting_confirmation.html?token=' . $token);
    //echo json_encode(['success' => 'OK', 'body' => "Le mail a été envoyé !"]);
} catch (Exception $e) {
    echo json_encode(['error' => 'NOK', 'body' => "Erreur d’envoi : {$mail->ErrorInfo}"]);
}


?>
