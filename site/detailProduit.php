<?php
require_once('./includes/head.php');
require_once('./includes/header.php');
?>
<link rel="stylesheet" href="./css/detailprod.css">
<main class="container my-5">
    <?php
    include "connect.inc.php";

    if (isset($_GET['idProduit'])) {
        $id = (int) htmlentities($_GET['idProduit']);

        $reqProduit = $conn->prepare("SELECT * FROM Produits p, Variantes v WHERE p.idProduit = :id AND p.idProduit = v.idProduit");
        $reqProduit->execute(['id' => $id]);

        if ($reqProduit->rowCount() < 1) {
            header('Location: accueil.php');
            exit();
        } else {
            $produit = $reqProduit->fetch();

            echo '<div class="row">';
            echo '<div class="col-md-6">';
            echo '<img src="https://picsum.photos/500/300?image=' . htmlspecialchars($produit['idProduit']+60) . '" alt="' . htmlspecialchars($produit['nomProduit']) . '" class="img-fluid rounded">';
            echo '</div>';
            echo '<div class="col-md-6">';
            echo '<h1>' . htmlspecialchars($produit['nomProduit']) . '</h1>';
            if ($produit['stock'] < 10) {
                echo '<span class="text-danger font-weight-bold">Il ne reste que ' . htmlspecialchars($produit['stock']) . ' en stock!</span>';
            } else {
                echo '<span class="text-success font-weight-bold">Disponible</span>';
            }
            echo '<h2 class="my-3">' . htmlspecialchars($produit['prix']) . ' €</h2>';

            // Récupère toutes les variantes
            $reqVariantes = $conn->prepare("SELECT * FROM Variantes WHERE idProduit = :id");
            $reqVariantes->execute(['id' => $id]);
            
            if ($reqVariantes->rowCount() > 0) {
                echo '<h4 id="coloris-text" class="mt-4">Coloris - '.$produit['variante'].'</h4>';
                echo '<div class="d-flex gap-3">';

                $first = true;
                while ($variante = $reqVariantes->fetch()) {
                    $color = htmlspecialchars($variante['variante']);
                    echo '
                        <label class="radio-container" style="--radio-color: ' . ($color === 'Rouge' ? '#e74c3c' : ($color === 'Bleu' ? '#3498db' : ($color === 'Noir' ? '#2c3e50' : '#bdc3c7'))) . ';">
                            <input type="radio" name="variante" value="' . $color . '" onclick="updateColoris(\'' . $color . '\')"' . ($first ? ' checked' : '') . '>
                            <span class="radio-checkmark"></span>
                        </label>
                    ';
                    $first = false;
                }

                echo '</div>';
            }

            echo '<button class="btn btn-dark btn-lg">Ajouter au panier</button>';
            echo '<div class="mt-4">';
            echo '<h4>Description</h4>';
            echo '<p>' . nl2br(htmlspecialchars($produit['description'])) . '</p>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        header('Location: accueil.php');
        exit();
    }
    ?>
</main>
<script>
    function updateColoris(color) {
        const colorisText = document.getElementById('coloris-text');
        colorisText.textContent = `Coloris - ${color}`;
    }
</script>
<?php require_once "./includes/footer.php"; ?>

</html>