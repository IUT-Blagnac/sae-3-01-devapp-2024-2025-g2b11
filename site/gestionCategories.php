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

// Ajouter une nouvelle catégorie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nomCategorie'])) {
    $nomCategorie = trim($_POST['nomCategorie']);
    $idPere = isset($_POST['idPere']) && $_POST['idPere'] !== '' ? (int)$_POST['idPere'] : null;

    if ($nomCategorie) {
        $stmt = $conn->prepare("INSERT INTO Categories (nomCategorie, idPere) VALUES (:nomCategorie, :idPere)");
        $stmt->execute([':nomCategorie' => $nomCategorie, ':idPere' => $idPere]);
        echo "<p style='color: green;'>Catégorie ajoutée avec succès.</p>";
    }
}

// Supprimer une catégorie
if (isset($_POST['deleteCategorie'])) {
    $idCategorie = (int)$_POST['idCategorie'];

    try {
        // Vérifier s'il y a des sous-catégories
        $stmtCheckChildren = $conn->prepare("SELECT COUNT(*) FROM Categories WHERE idPere = :idCategorie");
        $stmtCheckChildren->execute([':idCategorie' => $idCategorie]);
        $hasChildren = $stmtCheckChildren->fetchColumn() > 0;

        // Vérifier s'il y a des produits associés
        $stmtCheckProducts = $conn->prepare("SELECT COUNT(*) FROM Produits WHERE idCategorie = :idCategorie");
        $stmtCheckProducts->execute([':idCategorie' => $idCategorie]);
        $hasProducts = $stmtCheckProducts->fetchColumn() > 0;

        if ($hasChildren || $hasProducts) {
            $errorMessage = "Impossible de supprimer cette catégorie. ";
            if ($hasChildren) {
                $errorMessage .= "Elle contient des sous-catégories. ";
            }
            if ($hasProducts) {
                $errorMessage .= "Elle contient des produits associés.";
            }
            echo "<script>alert('$errorMessage');</script>";
        } else {
            $stmtDelete = $conn->prepare("DELETE FROM Categories WHERE idCategorie = :idCategorie");
            $stmtDelete->execute([':idCategorie' => $idCategorie]);
            echo "<p style='color: green;'>Catégorie supprimée avec succès.</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Erreur lors de la suppression de la catégorie : " . $e->getMessage() . "</p>";
    }
}

// Récupérer les catégories
$query = "SELECT * FROM Categories ORDER BY idPere, nomCategorie";
$stmt = $conn->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les Catégories</title>
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
            
            <h1>Gestion des Catégories</h1>

            <div class="add-category-button">
                <form method="POST">
                    <input type="text" name="nomCategorie" placeholder="Nom de la catégorie/sous-catégorie" required>
                    <select name="idPere">
                        <option value="">Aucune (Catégorie principale)</option>
                        <?php foreach ($categories as $categorie): ?>
                            <?php if ($categorie['idPere'] === null): ?>
                                <option value="<?php echo $categorie['idCategorie']; ?>"><?php echo $categorie['nomCategorie']; ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </form>
            </div>

            <!-- Tableau des catégories -->
            <table class="category-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Catégorie parente</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $categorie): ?>
                        <tr>
                            <td><?php echo htmlentities($categorie['idCategorie']); ?></td>
                            <td><?php echo htmlentities($categorie['nomCategorie']); ?></td>
                            <td>
                                <?php
                                if ($categorie['idPere'] === null) {
                                    echo "Aucune";
                                } else {
                                    $stmtPere = $conn->prepare("SELECT nomCategorie FROM Categories WHERE idCategorie = :idPere");
                                    $stmtPere->execute([':idPere' => $categorie['idPere']]);
                                    $pere = $stmtPere->fetch(PDO::FETCH_ASSOC);
                                    echo htmlentities($pere['nomCategorie']);
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                // Vérifier s'il y a des sous-catégories ou des produits associés
                                $stmtCheckChildren = $conn->prepare("SELECT COUNT(*) FROM Categories WHERE idPere = :idCategorie");
                                $stmtCheckChildren->execute([':idCategorie' => $categorie['idCategorie']]);
                                $hasChildren = $stmtCheckChildren->fetchColumn() > 0;

                                $stmtCheckProducts = $conn->prepare("SELECT COUNT(*) FROM Produits WHERE idCategorie = :idCategorie");
                                $stmtCheckProducts->execute([':idCategorie' => $categorie['idCategorie']]);
                                $hasProducts = $stmtCheckProducts->fetchColumn() > 0;

                                if (!$hasChildren && !$hasProducts): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="idCategorie" value="<?php echo $categorie['idCategorie']; ?>">
                                        <button type="submit" name="deleteCategorie" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">Supprimer</button>
                                    </form>
                                <?php else: ?>
                                    <button type="button" class="btn btn-danger" disabled>Supprimer</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
    <?php include './includes/footer.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.category-table tbody tr');
            rows.forEach(row => {
                row.addEventListener('mouseover', () => {
                    row.style.backgroundColor = '#f4a5a5';
                });
                row.addEventListener('mouseout', () => {
                    row.style.backgroundColor = '';
                });
            });
        });
    </script>
</body>
</html>
