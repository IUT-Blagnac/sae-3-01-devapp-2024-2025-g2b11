<?php
session_start();
include('connect.inc.php'); // Connexion PDO

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
                echo "<a href='detailProduit.php?idProduit=" . $result['idProduit'] . "' style='text-decoration: none; color: black;'>";
                echo "<div class='search-result-item'>";
                
                echo "<p>" . htmlspecialchars($result['nomProduit']) . " - <span style='font-size: 0.9rem; color: gray;'>" . htmlspecialchars($result['nomCategorie']) . "</span></p>";
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
