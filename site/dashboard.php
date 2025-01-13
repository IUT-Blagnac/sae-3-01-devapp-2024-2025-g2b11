<?php
session_start();

if (!isset($_SESSION['Sadmin']) or $_SESSION["Sadmin"] != true) {
    if(isset($_SESSION['Suser'])){
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once './includes/header.php'; ?>

    <div class="dashboard-container">
        <!-- Menu latéral -->
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

        <!-- Contenu principal -->
        <main class="dashboard-content">
            <h1>Dashboard Admin</h1>
            <section>
                <h2>Bienvenue, Administrateur</h2>
                <p>Utilisez le menu latéral pour naviguer entre les différentes sections du tableau de bord.</p>
            </section>
        </main>
    </div>

    <?php include './includes/footer.php'; ?>
</body>
</html>
<?php

?>