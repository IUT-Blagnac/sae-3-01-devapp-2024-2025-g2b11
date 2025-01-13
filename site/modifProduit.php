<link rel="stylesheet" href="./css/detailprod.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<?php
ob_start();

require_once './includes/head.php';
require_once './includes/header.php';


if (!isset($_SESSION['Sadmin']) or $_SESSION["Sadmin"] != true) {
    if (isset($_SESSION['Suser'])) {
        header("location: accueil.php");
        exit();
    }
    header("location: FormConnexion.php");
    exit();
}


include "connect.inc.php";
?>

<main class="container my-5">
    <?php

    if (isset($_POST['supprimer']) && $_POST['supprimer'] == true) {
        $id = (int) htmlentities($_POST['idProduit']);
        $variante = htmlentities($_POST['variante']);

        $reqSupprimer = $conn->prepare("UPDATE Variantes SET estSupprime = 1 WHERE idProduit = :idProduit AND variante = :variante");
        $reqSupprimer->execute(['idProduit' => $id, 'variante' => $variante]);
        echo "Variante supprimée avec succès.";
        header("location: modifProduit.php?idProduit={$id}");
    }

    if (isset($_POST['modifierProd']) && $_POST['modifierProd'] == true) {
        $id = (int) htmlentities($_POST['idProduit']);
        $nomProduit = htmlentities($_POST['nomProduit']);
        $description = $_POST['description'];
        $categorie = (int) htmlentities($_POST['categorie']);

        $reqProduit = $conn->prepare("UPDATE Produits SET nomProduit = :nomProduit, description = :description, idCategorie = :categorie WHERE idProduit = :id");
        $reqProduit->execute(['id' => $id, 'nomProduit' => $nomProduit, 'description' => $description, 'categorie' => $categorie]);
        echo "Produit modifié avec succès.";
    }

    if (isset($_POST['modifier']) && $_POST['modifier'] == true) {
        $id = (int) htmlentities($_POST['idProduit']);
        $variante = htmlentities($_POST['variante']);
        $prix = (float) htmlentities($_POST['prix']);
        $stock = (int) htmlentities($_POST['stock']);
        $reduction = (int) htmlentities($_POST['reduction']);
        if ($reduction == 0 || $reduction == "") {
            $reduction = 0;
        }

        if (isset($_FILES['imageProduit']) && $_FILES['imageProduit']['error'] == 0) {
            $allowedMimeTypes = ['image/png', 'image/jpg'];
            $fileMimeType = mime_content_type($_FILES['imageProduit']['tmp_name']);

            if (in_array($fileMimeType, $allowedMimeTypes)) {
                $uploadDir = 'images/p_images/';
                $uploadFile = $uploadDir . 'p' . $_POST['idProduit'] . '_' . $_POST['variante'] . '.png';

                if (move_uploaded_file($_FILES['imageProduit']['tmp_name'], $uploadFile)) {
                    echo "Image téléchargée avec succès.";
                } else {
                    echo "Erreur lors du téléchargement de l'image.";
                }
            } else {
                echo "Type de fichier non autorisé. Seuls les fichiers PNG sont autorisés.";
            }
        }

        $reqVariante = $conn->prepare("UPDATE Variantes SET prix = :prix, stock = :stock, reduction = :reduction WHERE idProduit = :id AND variante = :variante");
        $reqVariante->execute(['id' => $id, 'variante' => $variante, 'prix' => $prix, 'stock' => $stock, 'reduction' => $reduction]);
        echo "Variante modifiée avec succès.";
        header("location: modifProduit.php?idProduit={$id}");
    }

    if (isset($_POST['ajoutervarform']) && $_POST['ajoutervarform'] == true) {
        $id = (int) htmlentities($_POST['idProduit']);
        $variante = htmlentities($_POST['variante']);


        $reqCheckVariante = $conn->prepare("SELECT * FROM Variantes WHERE idProduit = :idProduit AND variante = :variante");
        $reqCheckVariante->execute(['idProduit' => $id, 'variante' => $variante]);

        if ($reqCheckVariante->rowCount() > 0) {
            $existingVariante = $reqCheckVariante->fetch();

            if ($existingVariante['estSupprime']) {
                $prix = (float) htmlentities($_POST['prix']);
                $stock = (int) htmlentities($_POST['stock']);
                $reduction = (int) htmlentities($_POST['reduction']);
                if ($reduction == 0 || $reduction == "") {
                    $reduction = null;
                }

                $reqUpdateVariante = $conn->prepare("UPDATE Variantes SET prix = :prix, stock = :stock, reduction = :reduction, estSupprime = 0 WHERE idProduit = :idProduit AND variante = :variante");
                $reqUpdateVariante->execute(['idProduit' => $id, 'variante' => $variante, 'prix' => $prix, 'stock' => $stock, 'reduction' => $reduction]);
                echo "Variante réactivée avec succès.";
            } else {
                echo "La variante existe déjà.";
            }
            if (isset($_FILES['imageProduit']) && $_FILES['imageProduit']['error'] == 0) {
                $allowedMimeTypes = ['image/png', 'image/jpg'];
                $fileMimeType = mime_content_type($_FILES['imageProduit']['tmp_name']);

                if (in_array($fileMimeType, $allowedMimeTypes)) {
                    $uploadDir = 'images/p_images/';
                    $uploadFile = $uploadDir . 'p' . $_POST['idProduit'] . '_' . $_POST['variante'] . '.png';

                    if (move_uploaded_file($_FILES['imageProduit']['tmp_name'], $uploadFile)) {
                        echo "Image téléchargée avec succès.";
                    } else {
                        echo "Erreur lors du téléchargement de l'image.";
                    }
                } else {
                    echo "Type de fichier non autorisé. Seuls les fichiers PNG sont autorisés.";
                }
            }

        } else {
            if (isset($_FILES['imageProduit']) && $_FILES['imageProduit']['error'] == 0) {
                $allowedMimeTypes = ['image/png', 'image/jpg'];
                $fileMimeType = mime_content_type($_FILES['imageProduit']['tmp_name']);

                if (in_array($fileMimeType, $allowedMimeTypes)) {
                    $uploadDir = 'images/p_images/';
                    $uploadFile = $uploadDir . 'p' . $_POST['idProduit'] . '_' . $_POST['variante'] . '.png';

                    if (move_uploaded_file($_FILES['imageProduit']['tmp_name'], $uploadFile)) {
                        $prix = (float) htmlentities($_POST['prix']);
                        $stock = (int) htmlentities($_POST['stock']);
                        $reduction = (int) htmlentities($_POST['reduction']);
                        if ($reduction == 0 || $reduction == "") {
                            $reduction = null;
                        }

                        $reqVariante = $conn->prepare("INSERT INTO Variantes (idProduit, variante, prix, stock, reduction) VALUES (:idProduit, :variante, :prix, :stock, :reduction)");
                        $reqVariante->execute(['idProduit' => $id, 'variante' => $variante, 'prix' => $prix, 'stock' => $stock, 'reduction' => $reduction]);
                        echo "Variante ajoutée avec succès.";
                    } else {
                        echo "Erreur lors du téléchargement de l'image.";
                    }
                } else {
                    echo "Type de fichier non autorisé. Seuls les fichiers PNG sont autorisés.";
                }
            }
        }
    }



    if (isset($_GET['idProduit']) && isset($_GET['ajouterVariante']) && $_GET['ajouterVariante'] == true) {
        $id = (int) htmlentities($_GET['idProduit']);
        echo "<form method='POST' action='modifProduit.php?idProduit={$id}' enctype='multipart/form-data'>";

        echo '<div class="form-group">';
        echo '<label for="variante">Variante</label>';
        echo '<input type="text" class="form-control" id="variante" name="variante" required>';
        echo '</div>';
        echo '<div class="form-group">';
        echo '<label for="prix">Prix</label>';
        echo '<input type="number" class="form-control" id="prix" name="prix" required>';
        echo '</div>';
        echo '<div class="form-group">';
        echo '<label for="stock">Stock</label>';
        echo '<input type="number" class="form-control" id="stock" name="stock" >';
        echo '</div>';
        echo '<div class="form-group">';
        echo '<label for="reduction">Réduction (%)</label>';
        echo '<input type="number" class="form-control" id="reduction" name="reduction">';
        echo '</div>';
        echo '<div class="form-group">';
        echo '<label for="imageProduit">Image du produit</label>';
        echo "<input type='file' class='form-control-file' id='imageProduit' name='imageProduit' accept='image/png'>";
        echo '</div>';

        echo "<input type='hidden' name='idProduit' value='$id'>";
        echo "<input type='hidden' name='ajoutervarform' value='true'>";

        echo '<button type="submit" class="btn btn-primary">Ajouter</button>';
        echo '</form>';
    }






    if (isset($_GET['idProduit'])) {
        $id = (int) htmlentities($_GET['idProduit']);

        $reqProduit = $conn->prepare("SELECT * FROM Produits WHERE idProduit = :id");
        $reqProduit->execute(['id' => $id]);

        if ($reqProduit->rowCount() < 1) {
            header('Location: accueil.php');
            exit();
        } else {
            $produit = $reqProduit->fetch();

            // Fetch categories
            $reqCategories = $conn->prepare("SELECT * FROM Categories");
            $reqCategories->execute();

            echo "<form method='POST' action='modifProduit.php?idProduit={$id}' enctype='multipart/form-data'>";
            echo '<div class="form-group">';
            echo '<label for="nomProduit">Nom du produit</label>';
            echo '<input type="text" class="form-control" id="nomProduit" name="nomProduit" value="' . $produit['nomProduit'] . '" required>';
            echo '</div>';
            echo '<div class="form-group">';
            echo '<label for="description">Description</label>';
            echo '<textarea class="form-control" id="description" name="description" rows="5" required>' . htmlspecialchars($produit['description']) . '</textarea>';
            echo '</div>';
            echo '<div class="form-group">';
            echo '<label for="categorie">Catégorie</label>';
            echo '<select class="form-control" id="categorie" name="categorie" required>';
            while ($categorie = $reqCategories->fetch()) {
                $selected = ($categorie['idCategorie'] == $produit['idCategorie']) ? 'selected' : '';
                echo "<option value='{$categorie['idCategorie']}' {$selected}>{$categorie['nomCategorie']}</option>";
            }
            echo '</select>';
            echo '</div>';
            echo '<input type="hidden" name="idProduit" value="' . $id . '">';
            echo '<input type="hidden" name="modifierProd" value="true">';
            echo '<button type="submit" class="btn btn-primary">Modifier le produit</button>';
            echo '</form><br><br>';





            $reqVariantes = $conn->prepare("SELECT * FROM Variantes WHERE idProduit = :id AND estSupprime = 0");
            $reqVariantes->execute(['id' => $id]);


            echo "<a href='modifProduit.php?idProduit={$id}&ajouterVariante=true' class='btn btn-primary'>Ajouter une variante</a>";
            if ($reqVariantes->rowCount() > 0) {
                echo '<div class="form-group">';
                echo '<label for="varianteSelect">Choisir une variante :</label>';
                echo '<select id="varianteSelect" class="form-control" onchange="location = this.value;">';
                echo '<option value="">Sélectionner une variante</option>';

                while ($variante = $reqVariantes->fetch()) {
                    $varianteName = htmlspecialchars($variante['variante']);
                    $selected = (isset($_GET['var']) && $_GET['var'] == $varianteName) ? 'selected' : '';
                    echo "<option value='modifProduit.php?idProduit={$id}&var={$varianteName}' {$selected}>{$varianteName}</option>";
                }
                echo '</select>';
                echo '</div>';
            }



            if (isset($_GET['var'])) {

                $reqVariante = $conn->prepare("SELECT * FROM Variantes WHERE idProduit = :id AND variante = :var");
                $reqVariante->execute(['id' => $id, 'var' => htmlentities($_GET['var'])]);
                if ($reqVariante->rowCount() > 0) {
                    $variante = $reqVariante->fetch();

                    echo '<div class="img-container position-relative" style="width: 50%; height: 260px; overflow: hidden; border-radius: 10px;">';
                    echo '<img src="./images/p_images/p' . htmlspecialchars($id) . '_' . htmlspecialchars($variante['variante']) . '.png" alt="' . htmlspecialchars($produit['nomProduit']) . '" style="width: 100%; height: auto;">';
                    echo '</div>';


                    echo "<form method='POST' action='modifProduit.php?idProduit={$id}&var={$variante['variante']}' enctype='multipart/form-data'>";

                    echo '<div class="form-group">';
                    echo '<label for="variante">Variante</label>';
                    echo '<input type="text" class="form-control" id="nomv" name="nomv" value="' . $variante['variante'] . '" disabled>';
                    echo '</div>';
                    echo '<div class="form-group">';
                    echo '<label for="prix">Prix</label>';
                    echo '<input type="number" class="form-control" id="prix" name="prix" value="' . $variante['prix'] . '" required>';
                    echo '</div>';
                    echo '<div class="form-group">';
                    echo '<label for="stock">Stock</label>';
                    echo '<input type="number" class="form-control" id="stock" name="stock" value="' . $variante['stock'] . '">';
                    echo '</div>';
                    echo '<div class="form-group">';
                    echo '<label for="reduction">Réduction (%)</label>';
                    echo '<input type="number" class="form-control" id="reduction" name="reduction" value="' . $variante['reduction'] . '">';
                    echo '</div>';
                    echo '<div class="form-group">';
                    echo '<label for="imageProduit">Image du produit</label>';
                    echo "<input type='file' class='form-control-file' id='imageProduit' name='imageProduit' accept='image/png'>";
                    echo '</div>';

                    echo "<input type='hidden' name='idProduit' value='$id'>";
                    echo "<input type='hidden' name='modifier' value='true'>";
                    echo "<input type='hidden' name='variante' value='{$variante['variante']}'>";

                    echo '<button type="submit" class="btn btn-primary">Modifier la variante</button>';
                    echo '</form>';

                    echo "<form method='POST' action='modifProduit.php?idProduit={$id}&var={$variante['variante']}'>";
                    echo "<input type='hidden' name='idProduit' value='$id'>";
                    echo "<input type='hidden' name='variante' value='{$variante['variante']}'>";
                    echo "<input type='hidden' name='supprimer' value='true'>";
                    echo '<button type="submit" class="btn btn-danger">Supprimer la variante</button>';
                    echo '</form>';


                }
            }
        }

    } else {
        header('Location: accueil.php');
        exit();
    }
















    ?>
</main>

<?php
ob_end_flush();
require_once "./includes/footer.php"; ?>

</html>