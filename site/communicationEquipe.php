<?php
session_start();
if (!isset($_SESSION['Sadmin']) or $_SESSION["Sadmin"] != true) {
    if (isset($_SESSION['Suser'])) {
        header("location: accueil.php");
        exit();
    }
    header("location: FormConnexion.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Communiquer avec l'équipe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once './includes/header.php'; ?>

    <div class="dashboard-container">
        <aside class="sidebar">
            <nav>
                <ul>
                    <li><a href="dashboard.php">Accueil</a></li>
                    <li><a href="gestionProduits.php">Gérer les produits</a></li>
                    <li><a href="gestionCategories.php">Gérer les catégories</a></li>
                    <li><a href="comptes.php">Gérer les comptes</a></li>
                    <li><a href="communicationEquipe.php">Communiquer avec l'équipe</a></li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <h1>Communiquer avec l'équipe</h1>
            <form action="traitCommunicationEquipe.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="adminEmail">Sélectionnez un administrateur :</label>
                    <select name="adminEmail" id="adminEmail" class="form-control" required>
                        <option value="admin1@example.com">admin1@example.com</option>
                        <option value="admin2@example.com">admin2@example.com</option>
                        <option value="admin3@example.com">admin3@example.com</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="motif">Motif :</label>
                    <select name="motif" id="motif" class="form-control" required>
                        <option value="bug">Bug</option>
                        <option value="mise_a_jour">Mise à jour</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="message">Message :</label>
                    <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
                </div>
                <div class="form-group">
                    <label for="screenshot">Capture d'écran (optionnel) :</label>
                    <input type="file" name="screenshot" id="screenshot" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </form>
        </main>
    </div>
    <?php include './includes/footer.php'; ?>
</body>
</html>
