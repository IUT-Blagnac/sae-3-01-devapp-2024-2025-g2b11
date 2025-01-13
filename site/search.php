<?php
session_start();
include('connect.inc.php'); // Connexion PDO

// Fonction pour mettre à jour l'historique des recherches dans la session
function updateSearchHistory($productId, $productName) {
    if (!isset($_SESSION['search_history'])) {
        $_SESSION['search_history'] = [];
    }

    // Vérifie si le produit est déjà dans l'historique
    if (!isset($_SESSION['search_history'][$productId])) {
        $_SESSION['search_history'][$productId] = [
            'name' => $productName,
            'count' => 0,
        ];
    }

    // Incrémente le compteur d'accès
    $_SESSION['search_history'][$productId]['count']++;
}

// Fonction pour récupérer l'historique des recherches depuis la session
function getSearchHistory() {
    return isset($_SESSION['search_history']) ? $_SESSION['search_history'] : [];
}

// Récupération de l'historique (action=history)
if (isset($_GET['action']) && $_GET['action'] === 'history') {
    $history = getSearchHistory();

    if (!empty($history)) {
        foreach ($history as $productId => $product) {
            echo "<div class='search-history-item' data-product-id='$productId'>";
            echo "<span class='history-name'>" . htmlspecialchars($product['name']) . " (" . $product['count'] . ")</span>";
            echo "<span class='delete-history'>&times;</span>";
            echo "</div>";
        }
    } else {
        echo "<div class='no-history'>Aucun historique de recherche.</div>";
    }
    exit;
}

// Recherche de produits (query)
if (isset($_GET['query']) && !empty($_GET['query'])) {
    $query = trim(htmlspecialchars($_GET['query']));

    $sql = "
        SELECT p.idProduit, p.nomProduit, c.nomCategorie 
        FROM Produits p
        JOIN Categories c ON p.idCategorie = c.idCategorie
        WHERE p.nomProduit REGEXP :query 
    ";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute(['query' => $query]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            foreach ($results as $result) {
                $productId = $result['idProduit'];
                $productName = htmlspecialchars($result['nomProduit']);
                echo "<a href='detailProduit.php?idProduit=$productId' class='search-result-link' data-product-id='$productId' data-product-name='$productName' style='text-decoration: none; color: black;'>";
                echo "<div class='search-result-item'>";
                echo "<p>$productName - <span style='font-size: 0.9rem; color: gray;'>" . htmlspecialchars($result['nomCategorie']) . "</span></p>";
                echo "</div></a>";
            }
        } else {
            echo "<div class='no-results'>Aucun produit correspondant.</div>";
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo "<div class='no-results'>Erreur lors de la recherche. Veuillez réessayer.</div>";
    }
} else {
    echo "<div class='no-results'>Aucun terme de recherche saisi.</div>";
}
?>

<script>
document.querySelectorAll('.search-result-link').forEach(link => {
    link.addEventListener('click', (event) => {
        const productId = event.target.closest('.search-result-link').dataset.productId;
        const productName = event.target.closest('.search-result-link').dataset.productName;

        // Mise à jour de l'historique via une requête AJAX
        fetch(`update_history.php?productId=${productId}&productName=${encodeURIComponent(productName)}`)
            .then(() => {
                window.location.href = event.target.closest('.search-result-link').href;
            })
            .catch(error => console.error('Erreur lors de la mise à jour de l\'historique :', error));
    });
});
</script>
