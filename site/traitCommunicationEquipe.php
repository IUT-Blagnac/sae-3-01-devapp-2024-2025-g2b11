<?php
session_start();
if (!isset($_SESSION['Sadmin']) or $_SESSION["Sadmin"] != true) {
    header("location: FormConnexion.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminEmail = $_POST['adminEmail'];
    $motif = $_POST['motif'];
    $message = $_POST['message'];
    $screenshot = $_FILES['screenshot'];

    $errors = [];

    if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresse email invalide.";
    }

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
        $subject = "Nouveau message de l'administrateur - Motif: $motif";
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

    header("location: communicationEquipe.php");
    exit();
} else {
    header("location: communicationEquipe.php");
    exit();
}
?>
