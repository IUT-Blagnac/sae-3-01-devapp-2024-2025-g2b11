<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $motif = $_POST['motif'];
    $message = $_POST['message'];
    $screenshot = $_FILES['screenshot'];

    $errors = [];

    if (empty($message)) {
        $errors[] = "Le message ne peut pas être vide.";
    }

    $screenshotPath = null;
    if ($screenshot['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $screenshotPath = $uploadDir . basename($screenshot['name']);
        if (!move_uploaded_file($screenshot['tmp_name'], $screenshotPath)) {
            $errors[] = "Erreur lors du téléchargement de la capture d'écran.";
        }
    }

    if (empty($errors)) {
        
        $adminEmail = "support@simul8.com";
        $subject = "Nouveau message d'assistance - Motif: $motif";
        $body = "Message: $message\n\n";
        if ($screenshotPath) {
            $body .= "Capture d'écran: $screenshotPath\n\n";
        }
        $headers = "From: no-reply@example.com";

        if (mail($adminEmail, $subject, $body, $headers)) {
            $_SESSION['message'] = "Message envoyé avec succès.";
        } else {
            $_SESSION['erreur'] = "Erreur lors de l'envoi du message.";
        }
    } else {
        $_SESSION['erreur'] = implode('<br>', $errors);
    }

    header("location: assistance.php");
    exit();
} else {
    header("location: assistance.php");
    exit();
}
?>
