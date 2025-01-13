<?php require_once './includes/head.php'; ?>
<?php require_once './includes/header.php'; ?>

<main class="affichage-produits">
    <?php
    include "connect.inc.php";

    echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">';
    echo '<link rel="stylesheet" href="./css/pageproduit.css">';

    echo '<div class="container-fluid ">';
    echo '<div class="row">';
    echo '<form method="GET" class="filtre-form mb-4 w-100">';

    echo '<div class="form-group mr-2">';
    echo '<input type="hidden" id="hidden_recherche" name="recherche" value="';
    echo isset($_GET['recherche']) ? htmlentities($_GET['recherche']) : '';
    echo '"></div>';

    // Liste déroulante des catégories
    echo '<div class="form-group mr-2">';
    echo '<select class="form-control" id="categorie" name="categorie">';
    echo '<option value="">Toutes les catégories</option>';

    $categories = $conn->prepare("SELECT idCategorie, nomCategorie FROM Categories WHERE idPere is NULL");
    $categories->execute();
    while ($categorie = $categories->fetch()) {
        $selected = (isset($_GET['categorie']) && $_GET['categorie'] == $categorie['idCategorie']) ? 'selected' : '';
        echo '<option value="' . htmlentities($categorie['idCategorie']) . '" ' . $selected . '>' . htmlentities($categorie['nomCategorie']) . '</option>';
    }
    echo '</select>';
    echo '</div>';

    // Liste déroulante des sous-catégories
    echo '<div class="form-group mr-2">';
    echo '<select class="form-control" id="sous_categorie" name="sous_categorie">';
    echo '<option value="">Toutes les sous-catégories</option>';

    if (isset($_GET['categorie']) && !empty($_GET['categorie'])) {
        $idCategorie = (int) htmlentities($_GET['categorie']);
        $sousCategories = $conn->prepare("SELECT idCategorie, nomCategorie FROM Categories WHERE idPere = :idCategorie");
        $sousCategories->execute([':idCategorie' => $idCategorie]);
        while ($sousCategorie = $sousCategories->fetch()) {
            $selected = (isset($_GET['sous_categorie']) && $_GET['sous_categorie'] == $sousCategorie['idCategorie']) ? 'selected' : '';
            echo '<option value="' . htmlentities($sousCategorie['idCategorie']) . '" ' . $selected . '>' . htmlentities($sousCategorie['nomCategorie']) . '</option>';
        }
    }
    echo '</select>';
    echo '</div>';

    // Tri par prix
    echo '<div class="form-group mr-2">';
    echo '<select class="form-control" id="tri_prix" name="tri_prix">';
    echo '<option value="asc" ' . (isset($_GET['tri_prix']) && $_GET['tri_prix'] == 'asc' ? 'selected' : '') . '>Croissant</option>';
    echo '<option value="desc" ' . (isset($_GET['tri_prix']) && $_GET['tri_prix'] == 'desc' ? 'selected' : '') . '>Décroissant</option>';
    echo '</select>';
    echo '</div>';

    // Prix minimum
    echo '<div class="form-group mr-2">';
    echo '<input type="number" class="form-control" id="prix_min" name="prix_min" placeholder="Prix Min" value="' . (isset($_GET['prix_min']) ? htmlentities($_GET['prix_min']) : '') . '">';
    echo '</div>';

    // Prix maximum
    echo '<div class="form-group mr-2">';
    echo '<input type="number" class="form-control" id="prix_max" name="prix_max" placeholder="Prix Max" value="' . (isset($_GET['prix_max']) ? htmlentities($_GET['prix_max']) : '') . '">';
    echo '</div>';

    // Liste déroulante des variantes
    echo '<div class="form-group mr-2">';
    echo '<select class="form-control" id="variante" name="variante">';
    echo '<option value="">Toutes les variantes</option>';

    $variantes = $conn->prepare("SELECT DISTINCT variante FROM Variantes v, Produits p WHERE v.idProduit = p.idProduit AND estDisponible = 1 AND estSupprime = 0");
    $variantes->execute();
    while ($variante = $variantes->fetch()) {
        $selected = (isset($_GET['variante']) && $_GET['variante'] == $variante['variante']) ? 'selected' : '';
        echo '<option value="' . htmlentities($variante['variante']) . '" ' . $selected . '>' . htmlentities($variante['variante']) . '</option>';
    }
    echo '</select>';
    echo '</div>';

    // Filtre stock
    echo '<div class="form-group form-check mr-2">';
    echo '<input type="checkbox" class="form-check-input" id="en_stock" name="en_stock" ' . (isset($_GET['en_stock']) ? 'checked' : '') . '>';
    echo '<label class="form-check-label" for="en_stock">En stock</label>';
    echo '</div>';

    // Filtre promo
    echo '<div class="form-group form-check mr-2">';
    echo '<input type="checkbox" class="form-check-input" id="en_promo" name="en_promo" ' . (isset($_GET['en_promo']) ? 'checked' : '') . '>';
    echo '<label class="form-check-label" for="en_promo">En promo</label>';
    echo '</div>';

    // Bouton de soumission
    echo '<div class="form-group mr-2">';
    echo '<button type="submit" class="btn btn-primary">Filtrer</button>';
    echo '</div>';

    echo '</form>';
    echo '</div>';
    echo '</div>';

    echo '<div class="container mt-5">';
    echo '<div class="row">';

    // Construction de la requête principale
    $requete = "
        SELECT *
        FROM Produits p, Variantes v
        WHERE p.idProduit = v.idProduit
        AND p.estDisponible = 1 AND v.estSupprime = 0
    ";
    $params = [];

    // recherche
    if (isset($_GET['recherche']) && !empty($_GET['recherche'])) {
        $recherche = '%' . htmlentities($_GET['recherche']) . '%';
        $requete .= " AND p.nomProduit LIKE :recherche";
        $params[':recherche'] = $recherche;
    }

    
    // filtre sous-catégorie
    if (isset($_GET['sous_categorie']) && !empty($_GET['sous_categorie'])) {
        $idSousCategorie = (int) htmlentities($_GET['sous_categorie']);
        $requete .= " AND p.idCategorie = :idSousCategorie ";
        $params[':idSousCategorie'] = $idSousCategorie;
    } else if (isset($_GET['categorie']) && !empty($_GET['categorie'])) {
        $idCategorie = (int) htmlentities($_GET['categorie']);
        $requete .= " AND (p.idCategorie = :idCategorie OR p.idCategorie IN (SELECT idCategorie FROM Categories WHERE idPere = :idCategorie))";
        $params[':idCategorie'] = $idCategorie;
    }

    // filtre prix min
    if (isset($_GET['prix_min']) && !empty($_GET['prix_min'])) {
        $requete .= " AND v.prix >= :prix_min";
        $params[':prix_min'] = (float) htmlentities($_GET['prix_min']);
    }

    // filtre  prix max
    if (isset($_GET['prix_max']) && !empty($_GET['prix_max'])) {
        $requete .= " AND v.prix <= :prix_max";
        $params[':prix_max'] = (float) htmlentities($_GET['prix_max']);
    }

    // filtre variante
    if (isset($_GET['variante']) && !empty($_GET['variante'])) {
        $variante = htmlentities($_GET['variante']);
        $requete .= " AND v.variante = :variante";
        $params[':variante'] = $variante;
    }

    //filtre du stock
    if (isset($_GET['en_stock'])) {
        $requete .= " AND (
            SELECT SUM(v_sub.stock) 
            FROM Variantes v_sub 
            WHERE v_sub.idProduit = p.idProduit
            AND v_sub.estSupprime = 0
        ) > 0";
    }

    //filtre promo
    if (isset($_GET['en_promo'])) {
        $requete .= " AND v.reduction is not null AND v.reduction > 0";
    }

    if (isset($_GET['tri_prix']) && $_GET['tri_prix'] == 'desc') {
        $requete .= " ORDER BY v.prix * (1 - COALESCE(v.reduction, 0) / 100) DESC";
    } else {
        $requete .= " ORDER BY v.prix * (1 - COALESCE(v.reduction, 0) / 100) ASC";
    }

    $reqProd = $conn->prepare($requete);
    $reqProd->execute($params);

    // Organisation des produits par idProduit
    $produits = [];
    while ($row = $reqProd->fetch()) {
        $idProduit = $row['idProduit'];

        // Regrouper les variantes par produit
        if (!isset($produits[$idProduit])) {
            $produits[$idProduit] = [
                'idProduit' => $idProduit,
                'nomProduit' => $row['nomProduit'],
                'idCategorie' => $row['idCategorie'],
                'variantes' => []
            ];
        }
        $produits[$idProduit]['variantes'][] = [
            'variante' => $row['variante'],
            'prix' => $row['prix'],
            'stock' => $row['stock'],
            'reduction' => $row['reduction']
        ];
    }

    // Affichage des produits
    if (empty($produits)) {
        echo '<div class="col-12">';
        echo '<p class="text-center">Aucun produit trouvé.</p>';
        echo '</div>';
    } else {
        foreach ($produits as $produit) {
            $variantechoisie = $produit['variantes'][0];  // Variante par défaut
        
            foreach ($produit['variantes'] as $variante) {
                if ($variante['stock'] > 0) {
                    $variantechoisie = $variante;  // Variante en stock choisie
                    break; // Sortir dès qu'on trouve une variante en stock
                }
            }

            // Affichage du produit
            $image = "./images/p_images/p" . ($produit['idProduit']) . '_' . ($variantechoisie['variante']) . ".png";
            echo '<div class="col-md-3 mb-4">';
            echo "<a href='detailProduit.php?idProduit=" . htmlentities($produit['idProduit']) . "&var=" . htmlentities($variantechoisie['variante']) . "' style='text-decoration: none; color: black;'>";
            echo '<div class="card h-100 position-relative" style="transition: transform 0.2s;">';
            echo "<img src='$image' class='card-img-top' alt='Product Image' style='height: 180px; object-fit: cover;'>";
            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . htmlentities($produit['nomProduit']) . '</h5>';

            if (isset($variantechoisie['reduction']) && $variantechoisie['reduction'] > 0) {
                $prixSolde = $variantechoisie['prix'] - ($variantechoisie['prix'] * $variantechoisie['reduction'] / 100);
                echo '<p class="card-text">Prix: <span style="text-decoration: line-through;">' . htmlentities($variantechoisie['prix']) . ' €<br></span> <span style="color: red; font-weight: bold;">' . htmlentities($prixSolde) . ' €</span></p>';
                echo '<span class="badge badge-danger position-absolute" style="top: 10px; right: 10px;">-' . htmlentities($variantechoisie['reduction']) . '%</span>';
            } else {
                echo '<p class="card-text">Prix: ' . htmlentities($variantechoisie['prix']) . ' €</p>';
            }

            if ($variantechoisie['stock'] == 0) {
                echo '<p class="card-text text-danger">Indisponible</p>';
            } elseif ($variantechoisie['stock'] < 10) {
                echo '<p class="card-text text-danger">Il en reste seulement ' . htmlentities($variantechoisie['stock']) . ' en stock!</p>';
            } else {
                echo '<p class="card-text text-success">En Stock</p>';
            }
            echo '</div>';
            echo '</div>';
            echo "</a>";
            echo '</div>';
        }
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

    // Synchroniser la recherche avec les filtres
    document.getElementById('recherche').addEventListener('input', function () {
        document.getElementById('hidden_recherche').value = this.value;
    });
</script>

<?php require_once "./includes/footer.php"; ?>
