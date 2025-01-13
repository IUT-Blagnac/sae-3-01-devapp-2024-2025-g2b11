<?php
require_once './includes/head.php';

include "connect.inc.php";

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['Suser'])) {
    header('Location: FormConnexion.php');
    exit();
}

// Vérifiez si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idCompte = $_SESSION['Suser'];
    $pays = htmlentities($_POST['pays']);
    $ville = htmlentities($_POST['ville']);
    $codePostal = htmlentities($_POST['codePostal']);
    $adresse = htmlentities($_POST['adresse']);
    $numeroBatiment = !empty($_POST['numeroBatiment']) ? htmlentities($_POST['numeroBatiment']) : null;
    $numeroAppartement = !empty($_POST['numeroAppartement']) ? htmlentities($_POST['numeroAppartement']) : null;

    try {
        // Insérer la nouvelle adresse dans la table Adresses
        $stmtInsertAdresse = $conn->prepare("
            INSERT INTO Adresses (pays, ville, codePostal, adresse, numeroBatiment, numeroAppartement)
            VALUES (:pays, :ville, :codePostal, :adresse, :numeroBatiment, :numeroAppartement)
        ");
        $stmtInsertAdresse->execute([
            ':pays' => $pays,
            ':ville' => $ville,
            ':codePostal' => $codePostal,
            ':adresse' => $adresse,
            ':numeroBatiment' => $numeroBatiment,
            ':numeroAppartement' => $numeroAppartement
        ]);

        // Récupérer l'ID de l'adresse insérée
        $idAdresse = $conn->lastInsertId();

        // Associer l'adresse au compte
        $stmtAssocierAdresse = $conn->prepare("
            INSERT INTO Resider (idCompte, idAdresse)
            VALUES (:idCompte, :idAdresse)
        ");
        $stmtAssocierAdresse->execute([
            ':idCompte' => $idCompte,
            ':idAdresse' => $idAdresse
        ]);

        // Redirection avec message de succès
        $_SESSION['message'] = "Votre adresse a été ajoutée avec succès.";
        header('Location: paiement.php');
        exit();
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
require_once './includes/header.php';
?>

<main class="container mt-5">
    <h1>Ajouter une Adresse</h1>
    <form action="ajouterAdresse.php" method="POST">
        <div class="form-group">
            <label for="pays" style="color: black;">Pays</label>
            <input type="text" class="form-control" id="pays" name="pays" required>
        </div>
        <div class="form-group">
            <label for="ville" style="color: black;">Ville</label>
            <input type="text" class="form-control" id="ville" name="ville" required>
        </div>
        <div class="form-group">
            <label for="codePostal" style="color: black;">Code Postal</label>
            <input type="text" class="form-control" id="codePostal" name="codePostal" required pattern="\d{5}" title="Veuillez entrer un code postal valide (5 chiffres)">
        </div>
        <div class="form-group">
            <label for="adresse" style="color: black;">Adresse</label>
            <input type="text" class="form-control" id="adresse" name="adresse" required>
        </div>
        <div class="form-group">
            <label for="numeroBatiment" style="color: black;">Numéro de Bâtiment (facultatif)</label>
            <input type="text" class="form-control" id="numeroBatiment" name="numeroBatiment">
        </div>
        <div class="form-group">
            <label for="numeroAppartement" style="color: black;">Numéro d'Appartement (facultatif)</label>
            <input type="text" class="form-control" id="numeroAppartement" name="numeroAppartement">
        </div>
        <button type="submit" class="btn btn-primary mt-3">Ajouter l'Adresse</button>
    </form>
</main>

<?php require_once './includes/footer.php'; ?>
