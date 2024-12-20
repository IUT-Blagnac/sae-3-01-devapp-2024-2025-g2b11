<?php require_once './includes/head.php'; ?>

<?php require_once './includes/header.php'; ?>
<main class="affichage-produits">
    <?php

    include "connect.inc.php";


    echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">';
    echo '<div class="container mt-5">';
    echo '<div class="row">';
    echo '<form method="GET" class="form-inline mb-4">';
    echo '<div class="form-group mr-2">';
    echo '<label for="categorie" class="mr-2" style="color: black;">ID Catégorie</label>';
    echo '<input type="number" class="form-control" id="categorie" name="categorie" value="' . (isset($_GET['categorie']) ? htmlentities($_GET['categorie']) : '') . '">';
    echo '</div>';
    echo '<div class="form-group mr-2">';
    echo '<label for="prix_min" class="mr-2" style="color: black;">Prix Min</label>';
    echo '<input type="number" class="form-control" id="prix_min" name="prix_min" value="' . (isset($_GET['prix_min']) ? htmlentities($_GET['prix_min']) : '') . '">';
    echo '</div>';
    echo '<div class="form-group mr-2">';
    echo '<label for="prix_max" class="mr-2" style="color: black;">Prix Max</label>';
    echo '<input type="number" class="form-control" id="prix_max" name="prix_max" value="' . (isset($_GET['prix_max']) ? htmlentities($_GET['prix_max']) : '') . '">';
    echo '</div>';
    echo '<button type="submit" class="btn btn-primary">Filtrer</button>';
    echo '</form>';
    echo '</div>';

    echo '<div class="row">';

    // Construction de la requête SQL
    // $requete = "
    // SELECT p.* 
    // FROM Produits p
    // JOIN Categories c ON p.idCategorie = c.idCategorie
    // WHERE 1=1
    $requete = "SELECT p.* FROM Produits p, Categories c 
                WHERE p.idCategorie = c.idCategorie";

    $params = [];

    // Filtre par catégorie parent (id)
    if (isset($_GET['categorie']) && !empty($_GET['categorie'])) {
        $idCategorie = (int) htmlentities($_GET['categorie']);

        // Inclure les produits liés à la catégorie parent ou à ses enfants
        $requete .= " AND (c.idCategorie = :idCategorie OR c.idPere = :idCategorie)";
        $params[':idCategorie'] = $idCategorie;
    }

    if (isset($_GET['recherche']) && !empty($_GET['recherche'])) {
        $recherche = '%' . htmlentities($_GET['recherche']) . '%';

        $requete .= " AND nomProduit LIKE :recherche";
        $params[':recherche'] = $recherche;
    }

    // Filtre par prix minimum
    if (isset($_GET['prix_min']) && !empty($_GET['prix_min'])) {
        $requete .= " AND prix >= :prix_min";
        $params[':prix_min'] = (float) htmlentities($_GET['prix_min']);
    }

    // Filtre par prix maximum
    if (isset($_GET['prix_max']) && !empty($_GET['prix_max'])) {
        $requete .= " AND prix <= :prix_max";
        $params[':prix_max'] = (float) htmlentities($_GET['prix_max']);
    }

    // Debug : Affichez la requête et les paramètres pour vérifier
// echo '<pre>';
// echo "Requête SQL : " . $requete . "\n";
// print_r($params);
// echo '</pre>';
    
    // Exécution de la requête
    $reqProd = $conn->prepare($requete);
    $reqProd->execute($params);

    $reqVariante = $conn->prepare("SELECT * FROM Variantes WHERE idProduit = :idProduit");

    $lsproduits = $reqProd->fetchAll();
    foreach ($lsproduits as $produit) {
        
        $reqVariante->execute([':idProduit' => $produit['idProduit']]);
        
        if ($reqVariante->rowCount() >= 1) {
            $variantes = $reqVariante->fetchAll();
            foreach ($variantes as $variante) {
                $variantechoisie = ($variante['stock'] > 1) ? $variante : $variantes[0];
            }
        }

        $image = "https://picsum.photos/360/360?image=" . $produit['idProduit'] + 60;
        echo '<div class="col-md-3 mb-4">';
        echo "<a href='detailProduit.php?idProduit=" . htmlentities($produit['idProduit']) . "' style='text-decoration: none; color: black;'>";
        echo '<div class="card h-100" style="transition: transform 0.2s;">';
        echo "<img src='$image' class='card-img-top' alt='Product Image'>";
        echo '<div class="card-body">';
        echo '<h5 class="card-title">' . htmlentities($produit['nomProduit']) . '</h5>';
        echo '<p class="card-text">Prix: ' . htmlentities($variantechoisie['prix']) . ' €</p>';

        //echo '<p class="card-text">Prix: 100 €</p>';
    
        if ($variantechoisie['stock'] < 10) {
             echo '<p class="card-text text-danger">Il en reste seulement ' . htmlentities($variantechoisie['stock']) . ' en stock!</p>';
         } else {
             echo '<p class="card-text text-success">En Stock</p>';
         }
        echo '<p class="card-text">Category ID: ' . htmlentities($produit['idCategorie']) . '</p>';
        echo '</div>';
        echo '</div>';
        echo "</a>";
        echo '</div>';
    }
    ?>
</main>
<script>
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('mouseover', () => {
            card.style.transform = 'scale(1.05)';
        });
        card.addEventListener('mouseout', () => {
            card.style.transform = 'scale(1)';
        });
    });
</script>


<?php require_once "./includes/footer.php"; ?>
