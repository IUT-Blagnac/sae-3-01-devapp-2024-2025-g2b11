<?php
// Inclus les fichiers nécessaires pour la structure et les connexions
require_once './includes/head.php';
require_once './includes/header.php';
include "connect.inc.php";


// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['Suser'])) {
    echo "<div class='container mt-5'><p class='text-danger'>Vous n'êtes pas connecté. Veuillez vous connecter pour accéder à votre panier.</p></div>";
    require_once "./includes/footer.php";
    exit();
}

// Récupérer le panier actif via la session
if (!isset($_SESSION['SidPanier'])) {
    echo "<div class='container mt-5'><p>Votre panier est vide.</p></div>";
    require_once "./includes/footer.php";
    exit();
}

$idCommande = $_SESSION['SidPanier'];

// Récupérer les produits du panier
$stmtProduits = $conn->prepare("
    SELECT p.nomProduit, q.variante, q.nbCommandee, q.estMontee, v.prix, v.stock, p.idProduit, p.description
    FROM Quantites q
    JOIN Produits p ON q.idProduit = p.idProduit
    JOIN Variantes v ON q.idProduit = v.idProduit AND q.variante = v.variante
    WHERE q.idCommande = :idCommande
");
$stmtProduits->execute([':idCommande' => $idCommande]);
$produits = $stmtProduits->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="affichage-produits">
    <div class="container mt-5">
        <h1 class="mb-4">Votre Panier</h1>
        <form action="panierTraitement.php" method="POST">
            <div class="cart-items">
                <?php
                if ($produits) {
                    $totalPanier = 0;

                    foreach ($produits as $produit) {
                        $image = "https://picsum.photos/360/360?image=" . ($produit['idProduit'] + 60);
                        $totalProduit = $produit['nbCommandee'] * $produit['prix'];
                        $totalPanier += $totalProduit;

                        echo '<div class="cart-item mb-4 d-flex align-items-center border-bottom pb-3">';
                        echo "<img src='$image' alt='Product Image' class='cart-item-image' style='width: 150px; height: 150px; margin-right: 15px;'>";
                        echo '<div class="cart-item-details flex-grow-1">';
                        echo '<h5 class="cart-item-title">' . htmlentities($produit['nomProduit']) . '</h5>';
                        echo '<p class="cart-item-description text-muted">' . htmlentities($produit['description']) . '</p>';
                        echo '<p class="cart-item-variante"><strong>Variante :</strong> ' . htmlentities($produit['variante']) . '</p>';
                        echo '<p class="cart-item-price"><strong>Prix unitaire :</strong> ' . htmlentities($produit['prix']) . ' €</p>';
                        echo '</div>';
                        echo '<div class="cart-item-quantity">';
                        echo '<div class="input-group">';
                        echo '<button type="submit" name="decrease" class="btn btn-outline-secondary" value="' . $produit['idProduit'] . '-' . $produit['variante'] . '">-</button>';
                        echo '<input type="number" class="form-control text-center quantity-input" name="quantite[' . $produit['idProduit'] . '][' . $produit['variante'] . ']" value="' . htmlentities($produit['nbCommandee']) . '" min="1" max="' . htmlentities($produit['stock']) . '" style="width: 60px;">';
                        echo '<button type="submit" name="increase" class="btn btn-outline-secondary" value="' . $produit['idProduit'] . '-' . $produit['variante'] . '">+</button>';
                        echo '</div>';
                        echo '</div>';
                        echo '<div class="cart-item-actions">';
                        echo '<p class="cart-item-total"><strong>Total :</strong> ' . htmlentities($totalProduit) . ' €</p>';
                        echo '<button type="submit" name="delete" class="btn btn-link text-danger p-0" value="' . $produit['idProduit'] . '-' . $produit['variante'] . '">Supprimer</button>';
                        echo '</div>';
                        echo '</div>'; // Fin cart-item
                    }

                    echo "<div class='cart-summary mt-4'>";
                    echo "<h3>Sous-total : <strong>{$totalPanier} €</strong></h3>";
                    echo '</div>';
                } else {
                    echo "<p>Votre panier est vide.</p>";
                }
                ?>
            </div>
        </form>
    </div>
</main>

<?php require_once "./includes/footer.php"; ?>
