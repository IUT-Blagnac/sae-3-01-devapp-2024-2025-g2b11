<?php
session_start();
if (!isset($_SESSION['Sadmin']) || $_SESSION["Sadmin"] != true) {
    if (isset($_SESSION['Suser'])) {
        header("location: accueil.php");
        exit();
    }
    header("location: FormConnexion.php");
    exit();
}

include "connect.inc.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomProduit = htmlentities($_POST['nomProduit']);
    $description = htmlentities($_POST['description']);
    $idCategorie = (int)$_POST['idCategorie'];
    
    try {
        $conn->beginTransaction();

        // Insertion du produit comme première variante
        $insertProduit = $conn->prepare("INSERT INTO Produits (nomProduit, description, idCategorie) VALUES (:nomProduit, :description, :idCategorie)");
        $insertProduit->execute([
            ':nomProduit' => $nomProduit,
            ':description' => $description,
            ':idCategorie' => $idCategorie
        ]);
        $idProduit = $conn->lastInsertId();

        // Gestion des variantes supplémentaires
        if (isset($_POST['variante'])) {
            $varianteCount = count($_POST['variante']);
            for ($i = 0; $i < $varianteCount; $i++) {
                $variante = htmlentities($_POST['variante'][$i]);
                $prix = isset($_POST['prix'][$i]) ? (float)$_POST['prix'][$i] : null;
                $stock = isset($_POST['stock'][$i]) ? (int)$_POST['stock'][$i] : null;
                $reduction = isset($_POST['reduction'][$i]) && $_POST['reduction'][$i] != "" ? (int)$_POST['reduction'][$i] : null;

                // Insertion de la variante
                $insertVariante = $conn->prepare("INSERT INTO Variantes (idProduit, variante, prix, stock, reduction) VALUES (:idProduit, :variante, :prix, :stock, :reduction)");
                $insertVariante->execute([
                    ':idProduit' => $idProduit,
                    ':variante' => $variante,
                    ':prix' => $prix,
                    ':stock' => $stock,
                    ':reduction' => $reduction
                ]);

                $idVariante = $conn->lastInsertId();

                // Gestion des images pour les variantes
                if (isset($_FILES['imageVariante']['error'][$i]) && $_FILES['imageVariante']['error'][$i] == 0) {
                    // Vérifier si l'image a été téléchargée pour la variante
                    if (isset($_FILES['imageVariante']['tmp_name'][$i]) && $_FILES['imageVariante']['tmp_name'][$i] != "") {
                        $fileMimeType = mime_content_type($_FILES['imageVariante']['tmp_name'][$i]);
                        $allowedMimeTypes = ['image/png', 'image/jpg', 'image/jpeg'];

                        // Vérifier si le type MIME est autorisé
                        if (in_array($fileMimeType, $allowedMimeTypes)) {
                            // Définir le répertoire de téléchargement
                            $uploadDir = 'images/p_images/';
                            
                            // Utilisation du format de nommage demandé : p{idProduit}_{nomVariante}.png
                            $uploadFile = $uploadDir . 'p' . $idProduit . '_' . urlencode($_POST['variante'][$i]) . '.png';

                            // Déplacer l'image téléchargée vers le répertoire de destination
                            if (move_uploaded_file($_FILES['imageVariante']['tmp_name'][$i], $uploadFile)) {
                                echo "L'image de la variante '{$variante}' a été téléchargée avec succès.<br>";
                            } else {
                                echo "Erreur lors du téléchargement de l'image de la variante.<br>";
                            }
                        } else {
                            echo "Type de fichier non autorisé pour l'image de la variante. Seuls les fichiers PNG/JPG sont acceptés.<br>";
                        }
                    }
                } else {
                    echo "Aucune image n'a été téléchargée pour la variante '{$variante}'.<br>";
                }
            }
        }

        $conn->commit();
        header("location: gestionProduits.php");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Erreur : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Produit</title>
    <link rel="stylesheet" href="./css/produits.css">
    <script>
        function addVariante() {
            const varianteSection = document.getElementById('varianteSection');
            const newVariante = document.createElement('div');
            newVariante.classList.add('variante-group');
            newVariante.innerHTML = `
                <div class="variante-fields">
                    <label>Nom de la variante :</label>
                    <input type="text" name="variante[]" required>

                    <label>Prix :</label>
                    <input type="number" name="prix[]" step="0.01" min="0" required>

                    <label>Stock :</label>
                    <input type="number" name="stock[]" min="0" required>

                    <label>Réduction (%) :</label>
                    <input type="number" name="reduction[]" min="0" max="90">

                    <label>Image de la variante :</label>
                    <input type="file" name="imageVariante[]" accept="image/png, image/jpg, image/jpeg" required>

                    <button type="button" class="btn-supprimer" onclick="removeVariante(this)">Supprimer</button>
                </div>
                <hr>
            `;
            varianteSection.appendChild(newVariante);
        }

        function removeVariante(button) {
            const varianteGroup = button.parentElement;
            varianteGroup.remove();
        }
    </script>
</head>
<body>
    <main>
        <div class="top-left-link">
            <a href="gestionProduits.php" class="btn btn-primary">← Retourner à la gestion des produits</a>
        </div>
        <h1>Ajouter un Produit</h1>
        <form method="POST" action="ajouterProduit.php" enctype="multipart/form-data">
            <!-- Nom et description du produit -->
            <label for="nomProduit">Nom du produit :</label>
            <input type="text" id="nomProduit" name="nomProduit" required>

            <label for="description">Description :</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <!-- Catégorie du produit -->
            <label for="idCategorie">Catégorie :</label>
            <select id="idCategorie" name="idCategorie" required>
                <?php
                // Récupération des catégories depuis la base de données
                $categories = $conn->query("SELECT idCategorie, nomCategorie FROM Categories")->fetchAll();
                foreach ($categories as $categorie) {
                    echo "<option value='{$categorie['idCategorie']}'>".htmlspecialchars($categorie['nomCategorie'])."</option>";
                }
                ?>
            </select>

            <!-- Variantes -->
            <fieldset id="varianteSection">
                <legend>Variantes</legend>

                <!-- Première variante (produit lui-même) -->
                <div class="variante-group">
                    <div class="variante-fields">
                        <label>Nom de la variante :</label>
                        <input type="text" name="variante[]" required>

                        <label>Prix :</label>
                        <input type="number" name="prix[]" step="0.01" min="0" required>

                        <label>Stock :</label>
                        <input type="number" name="stock[]" min="0" required>

                        <label>Réduction (%) :</label>
                        <input type="number" name="reduction[]" min="0" max="90">

                        <label>Image de la variante :</label>
                        <input type="file" name="imageVariante[]" accept="image/png, image/jpg, image/jpeg" required>
                    </div>
                    <hr>
                </div>
            </fieldset>

            <!-- Boutons pour ajouter une nouvelle variante -->
            <button type="button" onclick="addVariante()" class="btn btn-warning">Ajouter une variante</button>

            <!-- Bouton pour soumettre le formulaire -->
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </main>
</body>
</html>
