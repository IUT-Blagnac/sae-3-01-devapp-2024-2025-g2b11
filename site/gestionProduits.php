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

include "connect.inc.php";

// Rendre un produit indisponible
if (isset($_POST['make_unavailable'])) {
    $productIdToMakeUnavailable = $_POST['product_id'];
    $updateQuery = $conn->prepare("UPDATE Produits SET estDisponible = 0 WHERE idProduit = :idProduit");
    $updateQuery->execute([':idProduit' => $productIdToMakeUnavailable]);
    echo "<p style='color: green;'>Produit rendu indisponible avec succès.</p>";
}

// Rendre un produit disponible
if (isset($_POST['make_available'])) {
    $productIdToMakeAvailable = $_POST['product_id'];
    $updateQuery = $conn->prepare("UPDATE Produits SET estDisponible = 1 WHERE idProduit = :idProduit");
    $updateQuery->execute([':idProduit' => $productIdToMakeAvailable]);
    echo "<p style='color: green;'>Produit rendu disponible avec succès.</p>";
}

// Recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Requête SQL
$query = "
    SELECT p.idProduit, p.nomProduit, p.description, c.nomCategorie, p.estDisponible
    FROM Produits p
    JOIN Categories c ON p.idCategorie = c.idCategorie
";
if ($search) {
    $query .= " WHERE p.nomProduit LIKE :search OR c.nomCategorie LIKE :search";
}

$query .= " ORDER BY p.idProduit";

$produits = $conn->prepare($query);
if ($search) {
    $produits->execute([':search' => "%$search%"]);
} else {
    $produits->execute();
}
$productList = $produits->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les Produits</title>
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

        <main class="dashboard-content" style="width: 90%;">
            <h1>Gestion des Produits</h1>

            <div class="add-category-button">
                <a href="ajouterProduit.php" class="btn btn-primary">+ Ajouter un produit</a>
            </div>

            <!-- Barre de recherche -->
            <form method="GET" class="search-bar" style="width: 90%; margin-right: auto;">
                <input type="text" name="search" placeholder="Rechercher un produit..." value="<?php echo htmlentities($search); ?>">
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </form>

            <!-- Tableau des produits -->
            <table class="category-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Catégorie</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($produits->rowCount() > 0): ?>
                        <?php foreach ($productList as $product): ?>
                            <tr>
                                <td><?php echo htmlentities($product['idProduit']); ?></td>
                                <td><?php echo htmlentities($product['nomProduit']); ?></td>
                                <td><?php echo nl2br(htmlentities($product['description'])); ?></td>
                                <td><?php echo htmlentities($product['nomCategorie']); ?></td>
                                <td>
                                    <form method="GET" action="detailProduit.php" style="display: inline">
                                        <input type="hidden" name="idProduit" value="<?php echo $product['idProduit']; ?>">
                                        <button type="submit" class="btn btn-warning">Voir</button>
                                    </form>
                                    <form method="GET" action="modifProduit.php" style="display: inline">
                                        <input type="hidden" name="idProduit" value="<?php echo $product['idProduit']; ?>">
                                        <button type="submit" class="btn btn-info">Modifier</button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="product_id" value="<?php echo $product['idProduit']; ?>">
                                        <?php if ($product['estDisponible']): ?>
                                            <button type="submit" name="make_unavailable" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir rendre ce produit indisponible ?')">Rendre indisponible</button>
                                        <?php else: ?>
                                            <button type="submit" name="make_available" class="btn btn-success" onclick="return confirm('Êtes-vous sûr de vouloir rendre ce produit disponible ?')">Rendre disponible</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">Aucun produit trouvé.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
    <?php include './includes/footer.php'; ?>
</body>
</html>
