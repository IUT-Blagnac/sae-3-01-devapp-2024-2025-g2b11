<?php
// Inclus les fichiers nécessaires pour la structure et les connexions
require_once './includes/head.php';
require_once './includes/header.php';
include "connect.inc.php";
// Démarre la session pour accéder à $_SESSION

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['Suser'])) {
    header('Location: FormConnexion.php');
    exit();
}

if (isset($_SESSION['message'])) {
    echo "<div class='container mt-3 alert alert-success'>" . $_SESSION['message'] . "</div>";
    unset($_SESSION['message']); // Supprimer le message après affichage
}

if (isset($_SESSION['erreur'])) {
    echo "<div class='container mt-3 alert alert-danger'>" . $_SESSION['erreur'] . "</div>";
    unset($_SESSION['erreur']); // Supprimer le message après affichage
}

$idCompte = $_SESSION['Suser'];

// Vérifier si une commande avec `estPanier = 1` existe pour ce compte
$stmtCommande = $conn->prepare("
    SELECT idCommande FROM Commandes WHERE idCompte = :idCompte AND estPanier = 1
");
$stmtCommande->execute([':idCompte' => $idCompte]);
$commande = $stmtCommande->fetch(PDO::FETCH_ASSOC);

if (!$commande) {
    // Si aucune commande de type panier n'existe, en créer une
    $stmtNouvelleCommande = $conn->prepare("
        INSERT INTO Commandes (idCompte, estPanier) VALUES (:idCompte, 1)
    ");
    $stmtNouvelleCommande->execute([':idCompte' => $idCompte]);

    // Récupérer l'ID de la nouvelle commande
    $idCommande = $conn->lastInsertId();

    // Stocker l'ID du panier dans la session
    $_SESSION['SidPanier'] = $idCommande;
} else {
    // Si une commande existe, récupérer son ID
    $idCommande = $commande['idCommande'];
    $_SESSION['SidPanier'] = $idCommande;
}

// Récupérer les produits du panier
$stmtProduits = $conn->prepare("
    SELECT p.nomProduit, q.variante, q.nbCommandee, q.estMontee, v.prix, v.stock, p.idProduit, p.description, v.reduction, p.estDisponible, v.estSupprime
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
                        if ($produit['estDisponible'] == 0 || $produit['estSupprime'] == 1) {
                            $delProduitIndisponible = $conn->prepare("
                                DELETE FROM Quantites WHERE idProduit = :idProduit AND idCommande = :idCommande
                                ");
                                $delProduitIndisponible->execute([':idProduit' => $produit['idProduit'], ':idCommande' => $idCommande]);
                        }

                        $image = "./images/p_images/p" . ($produit['idProduit'])."_". ($produit['variante']).".png";
                        $prixReduit = $produit['prix'];
                        if (isset($produit['reduction']) && $produit['reduction'] > 0) {
                            $prixReduit = $produit['prix'] - ($produit['prix'] * $produit['reduction'] / 100);
                        }
                        $totalProduit = $produit['nbCommandee'] * $prixReduit;
                        $totalPanier += $totalProduit;

                        echo '<div class="cart-item mb-4 border-bottom pb-3 d-flex align-items-start flex-wrap">';

                        // Image du produit
                        echo '<div class="cart-item-image" style="flex: 0 0 150px; margin-right: 15px; overflow: hidden; border-radius: 10px;">';
                        echo "<a href='detailProduit.php?idProduit=" . htmlentities($produit['idProduit']) . "'>";
                        echo "<img src='$image' alt='Product Image' class='img-fluid' style='width: 175px; height: 150px; object-fit: cover; border-radius: 10px;'>";
                        echo "</a>";
                        echo '</div>';

                        // Détails du produit
                        echo '<div class="cart-item-details flex-grow-1" style="flex-basis: 300px;">';
                        echo '<h5 class="cart-item-title">' . htmlentities($produit['nomProduit']) . '</h5>';
                        echo '<p class="cart-item-description text-muted" style="margin-top: 10px;">' . htmlentities($produit['description']) . '</p>';
                        echo '<p class="cart-item-variante"><strong>Variante :</strong> ' . htmlentities($produit['variante']) . '</p>';
                        if (isset($produit['reduction']) && $produit['reduction'] > 0) {
                            echo '<p class="cart-item-price"><strong>Prix :</strong> <span style="text-decoration: line-through;">' . htmlentities($produit['prix']) . ' €</span> <span class="text-danger">' . htmlentities($prixReduit) . ' €</span></p>';
                        } else {
                            echo '<p class="cart-item-price"><strong>Prix :</strong> ' . htmlentities($produit['prix']) . ' €</p>';
                        }
                        if ($produit['stock'] < 10) {
                            echo '<p class="text-danger"><strong>Stock restant :</strong> ' . htmlentities($produit['stock']) . '</p>';
                        }
                        echo '</div>';

                        // Quantité et suppression
                        echo '<div class="cart-item-actions d-flex flex-column align-items-end" style="flex-basis: 150px;">';
                        
                        // Gestion des quantités
                        echo '<div class="input-group mb-2">';
                        if ($produit['stock'] > 0) {
                            echo '<button type="submit" name="decrease" class="btn btn-outline-secondary btn-sm" value="' . $produit['idProduit'] . '-' . $produit['variante'] . '">-</button>';
                            echo '<input type="number" class="form-control text-center mx-1" name="quantite[' . $produit['idProduit'] . '][' . $produit['variante'] . ']" value="' . htmlentities($produit['nbCommandee']) . '" min="1" max="' . htmlentities($produit['stock']) . '" style="width: 60px;">';
                            echo '<button type="submit" name="increase" class="btn btn-outline-secondary btn-sm" value="' . $produit['idProduit'] . '-' . $produit['variante'] . '">+</button>';
                        } else {
                            echo '<button type="button" class="btn btn-outline-secondary btn-sm" disabled>-</button>';
                            echo '<input type="number" class="form-control text-center mx-1" value="0" disabled style="width: 60px;">';
                            echo '<button type="button" class="btn btn-outline-secondary btn-sm" disabled>+</button>';
                        }
                        echo '</div>';

                        // Supprimer
                        echo '<div class="delete-item d-flex align-items-center">';
                        echo '<button type="submit" name="delete" class="btn btn-link text-danger d-flex align-items-center" value="' . $produit['idProduit'] . '-' . $produit['variante'] . '">';
                        echo '<img src="images/WEB_trash.png" alt="Supprimer" style="width: 20px; height: 20px; margin-right: 5px;">';
                        echo 'Supprimer</button>';
                        echo '</div>';

                        echo '<p class="mt-2"><strong>Total :</strong> ' . htmlentities($totalProduit) . ' €</p>';
                        echo '</div>'; 

                        echo '</div>'; 
                    }

                    echo "<div class='cart-summary mt-4'>";
                    echo "<h3>Sous-total : <strong>{$totalPanier} €</strong></h3>";
                    echo "<a href='paiement.php' class='btn btn-primary'>Passer au Paiement</a>";
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
