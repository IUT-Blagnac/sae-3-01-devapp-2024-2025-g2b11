<?php
require_once './includes/head.php';

include "connect.inc.php";

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['Suser'])) {
    header('Location: FormConnexion.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idCompte = $_SESSION['Suser'];
    $numeroCarte = $_POST['numeroCarte'];
    $dateExpiration = $_POST['dateExpiration'];
    $titulaire = $_POST['titulaire'];

    // Validation côté serveur
    $errors = [];
    if (!preg_match('/^\d{16}$/', $numeroCarte)) {
        $errors[] = "Le numéro de carte doit contenir exactement 16 chiffres.";
    }
    if (!preg_match('/^(0[1-9]|1[0-2])\d{2}$/', $dateExpiration)) {
        $errors[] = "La date d'expiration doit être au format MMYY (exemple : 1225 pour décembre 2025).";
    }
    if (empty(trim($titulaire))) {
        $errors[] = "Le titulaire de la carte ne peut pas être vide.";
    }

    if (empty($errors)) {
        try {
            // Ajouter la carte bancaire
            $stmtCarte = $conn->prepare("
                INSERT INTO CartesBancaire (numeroCarte, dateExpiration, titulaire) 
                VALUES (:numeroCarte, :dateExpiration, :titulaire)
            ");
            $stmtCarte->execute([
                ':numeroCarte' => $numeroCarte,
                ':dateExpiration' => $dateExpiration,
                ':titulaire' => $titulaire
            ]);

            // Lier la carte au compte utilisateur
            $stmtAppartenir = $conn->prepare("
                INSERT INTO Appartenir (idCompte, numeroCarte) 
                VALUES (:idCompte, :numeroCarte)
            ");
            $stmtAppartenir->execute([
                ':idCompte' => $idCompte,
                ':numeroCarte' => $numeroCarte
            ]);
            if(isset($_POST['modifcompte']) && $_POST['modifcompte'] == 'oui') {
                $_SESSION['message'] = "Votre carte bancaire a été ajoutée avec succès.";
                header('Location: modifMesinfos.php');
                exit();
            }
			
            $_SESSION['message'] = "Votre carte bancaire a été ajoutée avec succès.";
            header('Location: paiement.php');
            exit();
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }
	require_once './includes/header.php';
}
?>

<main class="container mt-5">
    <h1>Ajouter une Carte Bancaire</h1>
    <?php
    if (!empty($errors)) {
        echo '<div class="alert alert-danger">';
        foreach ($errors as $error) {
            echo '<p>' . htmlspecialchars($error) . '</p>';
        }
        echo '</div>';
    }
    ?>
    <form method="POST">
        <div class="form-group">
            <label for="numeroCarte" style="color: black;">Numéro de Carte</label>
            <input type="text" class="form-control" id="numeroCarte" name="numeroCarte" required pattern="\d{16}" title="Veuillez entrer un numéro de carte valide (16 chiffres)">
        </div>
        <div class="form-group">
            <label for="dateExpiration" style="color: black;">Date d'Expiration (MMYY)</label>
            <input type="text" class="form-control" id="dateExpiration" name="dateExpiration" required pattern="(0[1-9]|1[0-2])\d{2}" title="Format attendu : MMYY (exemple : 1225 pour décembre 2025)">
        </div>
        <div class="form-group">
            <label for="titulaire" style="color: black;">Titulaire de la Carte</label>
            <input type="text" class="form-control" id="titulaire" name="titulaire" required>
        </div>

        <?php if(isset($_GET['modifcompte']) && $_GET['modifcompte'] == 'ajoutercarte') { ?>
            <input type="hidden" name="modifcompte" value="oui"> 
        <?php } ?>
        <button type="submit" class="btn btn-success">Ajouter</button>
    </form>
</main>

<?php require_once './includes/footer.php'; ?>
