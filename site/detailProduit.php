<link rel="stylesheet" href="./css/detailprod.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">



<?php
ob_start();
require_once('./includes/head.php');
require_once('./includes/header.php');
include "connect.inc.php";

if (isset($_POST['suppReponse']) && $_POST['suppReponse'] == 'oui' && isset($_POST['idCompteAvis']) && isset($_POST['idProduit']) && isset($_SESSION['Sadmin']) && $_SESSION['Sadmin'] == true) {
    $idCompte = (int)htmlentities($_POST['idCompteAvis']);
    $idProduit = (int)htmlentities($_POST['idProduit']);

    $reqsuppReponse = $conn->prepare("UPDATE Avis SET reponse = NULL WHERE idCompte = :idCompte AND idProduit = :idProduit");
    $reqsuppReponse->execute(['idCompte' => $idCompte, 'idProduit' => $idProduit]);
    header("Location: detailProduit.php?idProduit={$idProduit}");
    exit();
}

?>


<main class="container my-5">
    <?php
    

    if (isset($_GET['idProduit'])) {
        $id = (int) htmlentities($_GET['idProduit']);

        // Récupère le produit et ses variantes
        $reqProduit = $conn->prepare("SELECT * FROM Produits p, Variantes v, Categories c WHERE p.idProduit = :id AND p.idProduit = v.idProduit AND p.idCategorie=c.idCategorie AND v.estSupprime=0");
        $reqProduit->execute(['id' => $id]);

        // Si le produit n'existe pas, redirige vers la page d'accueil
        if ($reqProduit->rowCount() < 1) {
            header('Location: accueil.php');
            exit();
        } else {
            // Récupère les données du produit
            $produit = $reqProduit->fetch();

            if($produit['estDisponible'] == 0){
                header('Location: accueil.php');
                exit();
            }

            // Vérifie si une variante est spécifiée dans l'URL
            if (isset($_GET['var'])) {
                $reqVariantes = $conn->prepare("SELECT * FROM Produits p, Variantes v, Categories c WHERE p.idProduit = :id AND p.idProduit = v.idProduit AND v.variante = :var AND p.idCategorie=c.idCategorie AND v.estSupprime=0");
                $reqVariantes->execute(['id' => $id, 'var' => htmlentities($_GET['var'])]);
                if ($reqVariantes->rowCount() > 0) {
                    $produit = $reqVariantes->fetch();
                }
            }


            echo '<div class="row">';
            echo '<div class="col-md-6">';
            echo '<div class="img-container position-relative" style="width: 100%; height: 355px; overflow: hidden; border-radius: 10px;">';
            echo '<img src="./images/p_images/p' . htmlspecialchars($produit['idProduit']) .'_'. htmlspecialchars($produit['variante']) .'.png" alt="' . htmlspecialchars($produit['nomProduit']) . '" style="width: 100%; height: auto;">';
            if (isset($produit['reduction']) && $produit['reduction'] > 0) {
                echo '<span class="badge badge-danger position-absolute" style="top: 10px; right: 10px;">-' . htmlspecialchars($produit['reduction']) . '%</span>';
            }
            echo '</div>';
            echo '</div>';
            echo '<div class="col-md-6">';
            echo '<h1>' . htmlspecialchars($produit['nomProduit']) . '</h1>';
            echo '<p class="text-muted">' . htmlspecialchars($produit['nomCategorie']) . '</p>';

            if (isset($_SESSION['Sadmin']) && $_SESSION['Sadmin'] == true) {
                echo '<a href="modifProduit.php?idProduit=' . htmlspecialchars($produit['idProduit']) . '" class="position-absolute" style="top: 10px; right: 10px;">';
                echo '<i class="fas fa-cog"></i>';
                echo '</a>';
            }

            // stock
            if ($produit['stock'] == 0) {
                echo '<span class="text-danger font-weight-bold">Indisponible</span>';

            } elseif ($produit['stock'] < 10) {
                echo '<span class="text-danger font-weight-bold">Il ne reste que ' . htmlspecialchars($produit['stock']) . ' en stock!</span>';
            } else {
                echo '<span class="text-success font-weight-bold">Disponible</span>';
            }

            
            if (isset($produit['reduction']) && $produit['reduction'] > 0) {
                $prixReduit = $produit['prix'] - $produit['prix'] * $produit['reduction'] / 100;
                echo '<h2 class="my-3"><span class="text-danger font-weight-bold">' . htmlspecialchars($prixReduit) . ' €</span>   <span  class="text-dark"><del>'    . htmlspecialchars($produit['prix']) . ' €</del></span></h2>';
                
            } else {
                echo '<h2 class="my-3">' . htmlspecialchars($produit['prix']) . ' €</h2>';
            }

            // Calcul de la moyenne des notes et du nombre d'avis
            $reqMoyenne = $conn->prepare("SELECT AVG(note) as moyenne, COUNT(*) as totalAvis FROM Avis WHERE idProduit = :idProd");
            $reqMoyenne->execute(['idProd' => $id]);
            $resultatAvis = $reqMoyenne->fetch();

            $moyenne = $resultatAvis['moyenne'];
            $totalAvis = $resultatAvis['totalAvis'];

            if ($moyenne !== null) {
                echo '<div class="mt-4">';

                // Affichage des étoiles pour la moyenne
                for ($i = 1; $i <= 5; $i++) {
                    echo $i <= round($moyenne)
                        ? '<i class="fas fa-star text-warning"></i>'
                        : '<i class="far fa-star"></i>';
                }

                echo '    ' . round($moyenne, 1) . ' (' . $totalAvis . ' avis)</p>';
                echo '</div>';
            }
            // Récupère et affiche les variantes disponibles
            $reqVariantes = $conn->prepare("SELECT * FROM Variantes WHERE idProduit = :id AND estSupprime=0");
            $reqVariantes->execute(['id' => $id]);

            if ($reqVariantes->rowCount() > 1) {
                echo '<h4 id="coloris-text" class="mt-4">Variantes disponibles</h4>';
                echo '<div class="d-flex flex-wrap gap-3">';

                // Affiche les boutons pour les variantes
                while ($variante = $reqVariantes->fetch()) {
                    $varianteName = htmlspecialchars($variante['variante']);
                    $isSelected = ($produit['variante'] == $varianteName) ? 'btn-danger' : 'btn-dark';

                    echo '
                        <a href="detailProduit.php?idProduit=' . $id . '&var=' . $varianteName . '" class="btn ' . $isSelected . ' mb-2 me-2" style="margin-right: 10px;">
                            ' . $varianteName . '
                        </a>
                    ';
                }

                echo '</div></br>';
            }

            // Bouton "Ajouter au panier"
            echo '
            <form action="ajouterPanier.php" method="POST">
                <input type="hidden" name="idProduit" value="' . $produit['idProduit'] . '">
                <input type="hidden" name="variante" value="' . $produit['variante'] . '">
                <button type="submit" name="ajouterPanier" class="btn btn-dark btn-lg">Ajouter au panier</button>
            </form>';
            echo '<div class="mt-4">';
            echo '<h4>Description</h4>';
            echo '<p>' . nl2br(htmlspecialchars($produit['description'])) . '</p>';
            echo '</div>';
            echo '</div>';
            echo '</div>';

            // Récupère et affiche les avis individuels
            $reqAvis = $conn->prepare("SELECT texteAvis, note, reponse, identifiant, a.idCompte FROM Avis a, Comptes c WHERE idProduit = :idProd AND a.idCompte = c.idCompte");
            $reqAvis->execute(['idProd' => $id]);

            if ($reqAvis->rowCount() > 0) {
                echo '<div class="mt-5">';
                echo '<h4>Derniers avis</h4>';
                echo '<div class="avis-container">'; // Conteneur flex avec défilement horizontal
    
                while ($avis = $reqAvis->fetch()) {
                    echo '
                     <div class="card" style="width: 18rem; margin-right: 1rem;">
                         <div class="card-body">
                             <div class="mb-2">';

                    // Générer les étoiles en fonction de la note
                    for ($i = 1; $i <= 5; $i++) {
                        echo $i <= round($avis['note'])
                            ? '<i class="fas fa-star text-warning"></i>'
                            : '<i class="far fa-star"></i>';
                    }

                    echo '  </div>
                             <h5 class="card-title">Avis de ' . htmlspecialchars($avis['identifiant']) . '</h5>
                             <p class="card-text">' . $avis['texteAvis'] . '</p>';

                    if (!empty($avis['reponse'])) {
                        echo '<p class="text-muted"><strong>Réponse :</strong> ' . $avis['reponse'] . '</p>';
                        if (isset($_SESSION['Sadmin']) && $_SESSION['Sadmin'] == true) {
                            echo "<form action='detailProduit.php?idProduit={$id}&var={$produit['variante']}' method='POST' style='position: absolute; bottom: 5px; right: 10px;'>";
                            echo '<input type="hidden" name="idCompteAvis" value="' . htmlspecialchars($avis['idCompte']) . '">';
                            echo '<input type="hidden" name="idProduit" value="' . $id . '">';
                            echo '<input type="hidden" name="suppReponse" value="oui">';
                            echo '<button type="submit" class="btn btn-link btn-sm text-muted" title="Supprimer cette réponse" onclick="return confirm(\'Voulez-vous vraiment supprimer cette réponse ?\');">';
                            echo '<i class="fas fa-times"></i>';
                            echo '</button>';
                            echo '</form>';
                        }
                    } else {
                        if (isset($_SESSION['Sadmin']) && $_SESSION['Sadmin'] == true) {
                            echo '<form action="repondreAvis.php" method="POST">';
                            echo '<input type="hidden" name="idCompteAvis" value="' . htmlspecialchars($avis['idCompte']) . '">';
                            echo '<input type="hidden" name="idProduit" value="' . $id . '">';
                            echo '<div class="form-group">';
                            echo '<label for="reponse">Réponse :</label>';
                            echo '<textarea class="form-control" id="reponse" name="reponse" rows="2" required></textarea>';
                            echo '</div>';
                            echo '<button type="submit" class="btn btn-dark">Répondre</button>';
                            echo '</form>';
                        }
                    }

                    // supp avis
                    if ((isset($_SESSION['Sadmin']) && $_SESSION['Sadmin'] == true) || (isset($_SESSION['Suser']) && $_SESSION['Suser'] == $avis['idCompte'])) {
                        echo '<form action="supprimerAvis.php" method="POST" style="position: absolute; top: 5px; right: 10px;">';
                        echo '<input type="hidden" name="idCompteAvis" value="' . htmlspecialchars($avis['idCompte']) . '">';
                        echo '<input type="hidden" name="idProduit" value="' . $id . '">';
                        echo '<button type="submit" class="btn btn-link btn-sm text-muted" title="Supprimer cet avis" onclick="return confirm(\'Voulez-vous vraiment supprimer cet avis ?\');">';
                        echo '<i class="fas fa-times"></i>';
                        echo '</button>';
                        echo '</form>';
                    }

                    // Modif avis
                    if ((isset($_SESSION['Sadmin']) && $_SESSION['Sadmin'] == true) || (isset($_SESSION['Suser']) && $_SESSION['Suser'] == $avis['idCompte'])) {
                        echo "<form action='detailProduit.php?idProduit={$id}&var={$produit['variante']}' method='POST' style='position: absolute; top: 5px; right: 30px;'>";
                        echo '<input type="hidden" name="idCompteAvis" value="' . htmlspecialchars($avis['idCompte']) . '">';
                        echo '<input type="hidden" name="idProduit" value="' . $id . '">';
                        echo '<button type="submit" class="btn btn-link btn-sm text-muted" title="Modifier cet avis">';
                        echo '<i class="fas fa-edit"></i>';
                        echo '</button>';
                        echo '</form>';
                    }

                    echo '  
                         </div>
                     </div>';
                }

                echo '</div>'; // Fin du conteneur flex
                echo '</div>'; // Fin de la section des avis
            }




            if (isset($_SESSION['Suser'])) {
                $reqCommande = $conn->prepare("SELECT COUNT(*) FROM Commandes c, Quantites q WHERE c.idCommande = q.idCommande
                                    AND c.idCompte = :idCompte AND q.idProduit = :idProduit AND c.estPanier = 0");
                $reqCommande->execute([
                    ':idCompte' => htmlentities($_SESSION['Suser']),
                    ':idProduit' => $id,
                ]);


                $countCommande = $reqCommande->fetchColumn();

                $reqDejaAvis = $conn->prepare("SELECT COUNT(*) FROM Avis WHERE idCompte = :idCompte AND idProduit = :idProduit");
                $reqDejaAvis->execute([
                    ':idCompte' => htmlentities($_SESSION['Suser']),
                    ':idProduit' => $id,
                ]);
                $countAvis = $reqDejaAvis->fetchColumn();

                if ($countCommande > 0) {
                    if ($countAvis < 1) {
                        echo '<div class="mt-5">';
                        echo '<h4>Ajouter un avis</h4>';
                        echo '<form action="ajouterAvis.php" method="POST">';
                        echo '<div class="form-group">';
                        echo '<label>Note :</label>';
                        echo '<div class="rating">';
                        for ($i = 5; $i >= 1; $i--) {
                            echo '<input type="radio" id="star' . $i . '" name="note" value="' . $i . '">';
                            echo '<label for="star' . $i . '" title="' . $i . ' étoiles">';
                            echo '<i class="fas fa-star"></i>';
                            echo '</label>';
                        }
                        echo '</div>';
                        echo '</div>';
                        echo '<div class="form-group">';
                        echo '<label for="texteAvis">Votre avis :</label>';
                        echo '<textarea class="form-control" id="texteAvis" name="texteAvis" rows="3"></textarea>';
                        echo '</div>';
                        echo '<input type="hidden" name="idProduit" value="' . $id . '">';
                        echo '<button type="submit" class="btn btn-dark mt-3">Soumettre</button>';
                        echo '</form>';
                        echo '</div>';

                    }
                }

            }

            //Modif avis
            if (isset($_POST['idCompteAvis']) && ((isset($_SESSION['Sadmin']) && $_SESSION['Sadmin'] == true) || (isset($_SESSION['Suser']) && $_SESSION['Suser'] == $_POST['idCompteAvis']))) {

                $reqSelectAvis = $conn->prepare("SELECT * FROM Avis a, Comptes c WHERE a.idCompte = :idCompte AND a.idProduit = :idProduit AND a.idCompte = c.idCompte");
                $reqSelectAvis->execute([
                    ':idCompte' => htmlentities($_POST['idCompteAvis']),
                    ':idProduit' => $id,
                ]);

                $avis = $reqSelectAvis->fetch();


                echo '<div class="mt-5">';
                if (isset($_SESSION['Sadmin']) && $_SESSION['Sadmin'] == true) {
                    echo '<h4>Modifier l\'avis de ' . $avis['identifiant'] . '</h4>';
                } else {
                    echo '<h4>Modifier mon avis</h4>';
                }
                echo '<form action="modifierAvis.php" method="POST">';
                echo '<div class="form-group">';
                echo '<label>Note :</label>';
                echo '<div class="rating">';

                for ($i = 5; $i >= 1; $i--) {
                    echo '<input type="radio" id="star' . $i . '" name="note" value="' . $i . '" ' . ($i == $avis['note'] ? 'checked' : '') . '>';
                    echo '<label for="star' . $i . '" title="' . $i . ' étoiles">';
                    echo '<i class="fas fa-star"></i>';
                    echo '</label>';
                }
                echo '</div>';
                echo '</div>';
                echo '<div class="form-group">';
                echo '<label for="texteAvis">Votre avis :</label>';
                echo '<textarea class="form-control" id="texteAvis" name="texteAvis" rows="3">' . $avis['texteAvis'] . '</textarea>';
                echo '</div>';
                echo '<input type="hidden" name="idProduit" value="' . $id . '">';
                echo '<input type="hidden" name="idCompteAvis" value="' . $avis['idCompte'] . '">';
                echo '<button type="submit" class="btn btn-dark mt-3">Modifier</button>';
                echo '</form>';
                echo '</div>';



            }





            //Produit similaire
            $reqSimilaire = $conn->prepare("SELECT p.*, v.variante FROM Produits p ,Variantes v WHERE p.idProduit = v.idProduit AND idCategorie = :idCat AND p.idProduit != :idProd AND v.estSupprime=0 AND p.estDisponible=1 ORDER BY RAND() LIMIT 4");
            $reqSimilaire->execute(['idCat' => $produit['idCategorie'], 'idProd' => $id]);

            if ($reqSimilaire->rowCount() > 0) {
                echo '<div class="mt-5">';
                echo '<h4>Produits qui pourraient vous plaire</h4>';
                echo '<div class="row">';

                while ($similaire = $reqSimilaire->fetch()) {
                    echo '
                    <div class="col-md-3 mb-4">';
                    echo "<a href='detailProduit.php?idProduit=" . htmlentities($similaire['idProduit']) . "' style='text-decoration: none; color: black;'>";

                    echo '<div class="product-card h-100 card" style="transition: transform 0.2s;">
                            <img src="./images/p_images/p' . htmlspecialchars($similaire['idProduit']) .'_'. htmlspecialchars($similaire['variante']).'.png" alt="' . htmlspecialchars($similaire['nomProduit']) . '" class="product-card-img-top">
                            <div class="product-card-body">
                                <h5 class="product-card-title">' . htmlspecialchars($similaire['nomProduit']) . '</h5>
                                <a href="detailProduit.php?idProduit=' . $similaire['idProduit'] . '" class="btn btn-dark btn-bottom-right mt-3">Voir le produit</a>
                            </div>
                        </div>
                    </div>';
                }
                echo '</div>';
                echo '</div>';
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