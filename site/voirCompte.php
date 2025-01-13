<?php
include "connect.inc.php";
session_start();
if (!isset($_SESSION['Sadmin']) or $_SESSION["Sadmin"] != true) {
    if (isset($_SESSION['Suser'])) {
        header("location: accueil.php");
        exit();
    }
    header("location: FormConnexion.php");
    exit();
}

?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="./css/voirCompte.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">



<?php

if (isset($_POST['changerMdp']) && $_POST['changerMdp'] == 'ok') {
    $idCompte = intval(htmlentities($_GET['idCompte']));
    $nouveauMdp = htmlentities($_POST['nouveauMdp']);
    $confirmerMdp = htmlentities($_POST['confirmerMdp']);

    if ($nouveauMdp != $confirmerMdp) {
        echo "<script>alert('Les mots de passe ne correspondent pas.'); window.history.back();</script>";
        exit();
    }

    $req = $conn->prepare("UPDATE Comptes SET mdp = :mdp WHERE idCompte = :idCompte");
    $req->execute(['mdp' => password_hash($nouveauMdp, PASSWORD_BCRYPT), 'idCompte' => $idCompte]);
    header("location: voirCompte.php?idCompte={$idCompte}");
    exit();
}


if (isset($_POST['enregistrer']) && $_POST['enregistrer'] == 'ok') {
    $idCompte = intval(htmlentities($_POST['idCompte']));
    $identifiant = htmlentities($_POST['identifiant']);
    $nom = htmlentities($_POST['nom']);
    $prenom = htmlentities($_POST['prenom']);
    $email = htmlentities($_POST['email']);
    $numeroTelephone = htmlentities($_POST['numeroTelephone']);

    $reqCheck = $conn->prepare("SELECT COUNT(*) FROM Comptes WHERE (identifiant = :identifiant OR email = :email) AND idCompte != :idCompte");
    $reqCheck->execute(['identifiant' => $identifiant, 'email' => $email, 'idCompte' => $idCompte]);
    $count = $reqCheck->fetchColumn();

    if ($count > 0) {
        echo "<script>alert('L\'identifiant ou l\'email est déjà utilisé.'); window.history.back();</script>";
        exit();
    }

    $req = $conn->prepare("UPDATE Comptes SET identifiant = :identifiant, nom = :nom, prenom = :prenom, email = :email, numeroTelephone = :numeroTelephone WHERE idCompte = :idCompte");
    $req->execute(['identifiant' => $identifiant, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'numeroTelephone' => $numeroTelephone, 'idCompte' => $idCompte]);
    header("location: voirCompte.php?idCompte={$idCompte}");
    exit();
}

if (isset($_POST['enregistrerAdr']) && $_POST['enregistrerAdr'] == 'ok') {
    $idAdresse = intval(htmlentities($_POST['idAdresse']));
    $adresse = htmlentities($_POST['adresse']);
    $ville = htmlentities($_POST['ville']);
    $pays = htmlentities($_POST['pays']);
    $codePostal = htmlentities($_POST['codePostal']);
    $numeroAppartement = htmlentities($_POST['numeroAppartement']);
    $numeroBatiment = htmlentities($_POST['numeroBatiment']);

    $req = $conn->prepare("UPDATE Adresses SET adresse = :adresse, ville = :ville, pays = :pays, codePostal = :codePostal, numeroAppartement = :numeroAppartement, numeroBatiment = :numeroBatiment WHERE idAdresse = :idAdresse");
    $req->execute(['adresse' => $adresse, 'ville' => $ville, 'pays' => $pays, 'codePostal' => $codePostal, 'numeroAppartement' => $numeroAppartement, 'numeroBatiment' => $numeroBatiment, 'idAdresse' => $idAdresse]);
    header("location: voirCompte.php?idCompte={$_GET['idCompte']}");
    exit();
}
if (isset($_GET['supprimerAdresse']) && !empty($_GET['supprimerAdresse'] && isset($_GET['idCompte']))) {
    $idAdresse = intval(htmlentities($_GET['supprimerAdresse']));
    $idCompte = intval(htmlentities($_GET['idCompte']));
    $req = $conn->prepare("DELETE FROM Resider WHERE idAdresse = :idAdresse AND idCompte = :idCompte");
    $req->execute(['idAdresse' => $idAdresse, 'idCompte' => $idCompte]);
    $req = $conn->prepare("DELETE FROM Adresses WHERE idAdresse = :idAdresse");
    $req->execute(['idAdresse' => $idAdresse]);
    header("location: voirCompte.php?idCompte={$_GET['idCompte']}");
    exit();
}

if (isset($_POST['supprimerCarte']) && !empty($_POST['supprimerCarte'] && isset($_GET['idCompte']))) {
    $numeroCarte = htmlentities($_POST['supprimerCarte']);
    $idCompte = intval(htmlentities($_GET['idCompte']));

    $req = $conn->prepare("DELETE FROM Appartenir WHERE numeroCarte = :numeroCarte AND idCompte = :idCompte");
    $req->execute(['numeroCarte' => $numeroCarte, 'idCompte' => $idCompte]);
    $req = $conn->prepare("DELETE FROM CartesBancaire WHERE numeroCarte = :numeroCarte");
    $req->execute(['numeroCarte' => $numeroCarte]);
    header("location: voirCompte.php?idCompte={$_GET['idCompte']}");
    exit();
}

?>

<main class="container mt-5">
    <?php
    if (isset($_GET['idCompte'])) {
        $idCompte = intval(htmlentities($_GET['idCompte']));


        $req = $conn->prepare("SELECT * FROM Comptes WHERE idCompte = :id");
        $req->execute(['id' => $idCompte]);
        $utilisateur = $req->fetch();

        if ($utilisateur) {
            echo "<h1 class='text-center'>Gestion de l'utilisateur : {$utilisateur['identifiant']} ";
            if ($utilisateur['estInactif']) {
                echo "<p class='text-danger'><strong>Compte inactif</strong></p>";
            }
            echo "</h1>";
            echo "<div class='row'>";

            // infos compte
            echo "<div class='col-md-6 mb-4'>";
            echo "<div class='card'>";
            echo "<div class='card-body position-relative'>";

            if (isset($_GET['modifier']) && $_GET['modifier'] == true) {
                // modif info
                echo "<form method='POST' action='voirCompte.php?idCompte={$idCompte}'>";
                echo "<input type='hidden' name='idCompte' value='{$utilisateur['idCompte']}'>";
                echo "<div class='mb-3'>";
                echo "<label for='identifiant' class='form-label'>Identifiant</label>";
                echo "<input type='text' class='form-control' id='identifiant' name='identifiant' value='{$utilisateur['identifiant']}' required>";
                echo "</div>";
                echo "<div class='mb-3'>";
                echo "<label for='nom' class='form-label'>Nom</label>";
                echo "<input type='text' class='form-control' id='nom' name='nom' value='{$utilisateur['nom']}' required>";
                echo "</div>";
                echo "<div class='mb-3'>";
                echo "<label for='prenom' class='form-label'>Prénom</label>";
                echo "<input type='text' class='form-control' id='prenom' name='prenom' value='{$utilisateur['prenom']}' required>";
                echo "</div>";
                echo "<div class='mb-3'>";
                echo "<label for='email' class='form-label'>Email</label>";
                echo "<input type='email' class='form-control' id='email' name='email' value='{$utilisateur['email']}' pattern='^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$' required>";
                echo "</div>";
                echo "<div class='mb-3'>";
                echo "<label for='numeroTelephone' class='form-label'>Tel</label>";
                echo "<input type='text' class='form-control' id='numeroTelephone' name='numeroTelephone' value='{$utilisateur['numeroTelephone']}' pattern='^\\+?[0-9]{10,15}$' required>";
                echo "</div>";
                echo "<div class='mb-3'>";
                echo "<label for='role' class='form-label'>Rôle</label>";
                echo "<p class='form-control-plaintext'>" . ($utilisateur['estAdmin'] ? "Administrateur" : "Utilisateur") . "</p>";
                echo "</div>";
                echo "<button type='submit' name='enregistrer' value='ok' class='btn btn-dark'>Enregistrer</button>";
                echo "<a href='voirCompte.php?idCompte={$idCompte}' class='btn btn-secondary'>Annuler</a>";
                echo "</form>";
            } else if (isset($_GET['mdp']) && $_GET['mdp'] == 'true') {
                echo "<form method='POST' action='voirCompte.php?idCompte={$idCompte}'>";
                echo "<div class='mb-3'>";
                echo "<label for='nouveauMdp' class='form-label'>Nouveau mot de passe</label>";
                echo "<input type='password' class='form-control' id='nouveauMdp' name='nouveauMdp' required>";
                echo "</div>";
                echo "<div class='mb-3'>";
                echo "<label for='confirmerMdp' class='form-label'>Confirmer le mot de passe</label>";
                echo "<input type='password' class='form-control' id='confirmerMdp' name='confirmerMdp' required>";
                echo "</div>";
                echo "<button type='submit' name='changerMdp' value='ok' class='btn btn-dark'>Changer le mot de passe</button>";
                echo "<a href='voirCompte.php?idCompte={$idCompte}' class='btn btn-secondary'>Annuler</a>";
                echo "</form>";
                
            }else {
                // Affichage des informations
                echo "<a href='?idCompte={$utilisateur['idCompte']}&modifier=true' class='btn btn-secondary btn-sm position-absolute' style='top: 10px; right: 10px;'>";
                echo "<i class='fas fa-pencil-alt'></i> Modifier";
                echo "</a>";

                echo "<h2 class='card-title'>Information du compte</h2>";
                echo "<p>ID : {$utilisateur['idCompte']}</p>";
                echo "<p>Identifiant : {$utilisateur['identifiant']}</p>";
                echo "<p>Nom : {$utilisateur['nom']}</p>";
                echo "<p>Prénom : {$utilisateur['prenom']}</p>";
                echo "<p>Email : {$utilisateur['email']}</p>";
                echo "<p>Tel : {$utilisateur['numeroTelephone']}</p>";
                echo "<p>Rôle : " . ($utilisateur['estAdmin'] ? "Administrateur" : "Utilisateur") . "</p>";
                echo "<p> Points de fidélité : {$utilisateur['pointF']}</p>";

                echo "<form method='GET' action='voirCompte.php?idCompte={$idCompte}' class='mt-3'>";
                echo "<input type='hidden' name='idCompte' value='{$idCompte}'>";
                echo "<input type='hidden' name='mdp' value='true'>";
                echo "<button type='submit' class='btn btn-warning'>Réinitialiser le mot de passe</button>";
                echo "</form>";
            }

            echo "</div>";
            echo "</div>";
            echo "</div>";

            // Section des adresses
            echo "<div class='col-md-6 mb-4'>";
            echo "<div class='card'>";
            echo "<div class='card-body'>";
            echo "<h2 class='card-title'>Adresses</h2>";
            $reqAdr = $conn->prepare("SELECT * FROM Adresses A, Resider R WHERE A.idAdresse=R.idAdresse AND R.idCompte = :id");
            $reqAdr->execute(['id' => $idCompte]);
            if ($reqAdr->rowCount() > 0) {
                $adresses = $reqAdr->fetchAll();
                foreach ($adresses as $adresse) {
                    if (isset($_GET['modifierAdresse']) && $_GET['modifierAdresse'] == $adresse['idAdresse']) {
                        echo "<form method='POST' action='voirCompte.php?idCompte={$idCompte}'>";
                        echo "<input type='hidden' name='idAdresse' value='{$adresse['idAdresse']}'>";
                        echo "<div class='mb-3'>";
                        echo "<label for='adresse' class='form-label'>Adresse</label>";
                        echo "<input type='text' class='form-control' id='adresse' name='adresse' value='{$adresse['adresse']}' required>";
                        echo "</div>";
                        echo "<div class='mb-3'>";
                        echo "<label for='ville' class='form-label'>Ville</label>";
                        echo "<input type='text' class='form-control' id='ville' name='ville' value='{$adresse['ville']}' required>";
                        echo "</div>";
                        echo "<div class='mb-3'>";
                        echo "<label for='pays' class='form-label'>Pays</label>";
                        echo "<input type='text' class='form-control' id='pays' name='pays' value='{$adresse['pays']}' required>";
                        echo "</div>";
                        echo "<div class='mb-3'>";
                        echo "<label for='codePostal' class='form-label'>Code Postal</label>";
                        echo "<input type='text' class='form-control' id='codePostal' name='codePostal' value='{$adresse['codePostal']}' required>";
                        echo "</div>";
                        echo "<div class='mb-3'>";
                        echo "<label for='numeroAppartement' class='form-label'>Numéro d'Appartement</label>";
                        echo "<input type='text' class='form-control' id='numeroAppartement' name='numeroAppartement' value='{$adresse['numeroAppartement']}'>";
                        echo "</div>";
                        echo "<div class='mb-3'>";
                        echo "<label for='numeroBatiment' class='form-label'>Numéro de Bâtiment</label>";
                        echo "<input type='text' class='form-control' id='numeroBatiment' name='numeroBatiment' value='{$adresse['numeroBatiment']}'>";
                        echo "</div>";
                        echo "<button type='submit' name='enregistrerAdr' value='ok' class='btn btn-dark'>Enregistrer</button>";
                        echo "<a href='voirCompte.php?idCompte={$idCompte}' class='btn btn-secondary'>Annuler</a>";
                        echo "</form>";
                    } else {
                        echo "<div class='d-flex justify-content-between align-items-center mb-2'>";
                        echo "<p>{$adresse['adresse']} {$adresse['ville']} {$adresse['pays']} {$adresse['codePostal']} {$adresse['numeroAppartement']} {$adresse['numeroBatiment']}</p>";
                        echo "<div>";
                        echo "<a href='voirCompte.php?idCompte={$idCompte}&modifierAdresse={$adresse['idAdresse']}' class='btn btn-sm me-2'><i class='fas fa-pencil-alt'></i></a>";
                        echo "<a href='voirCompte.php?idCompte={$idCompte}&supprimerAdresse={$adresse['idAdresse']}' class='btn btn-sm' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cette adresse ?\");'><i class='fas fa-trash-alt'></i></a>";
                        echo "</div>";
                        echo "</div>";
                    }
                }
            } else {
                echo "<p>Aucune adresse enregistrée.</p>";
            }
            echo "</div>";
            echo "</div>";
            echo "</div>";

            echo "</div>"; 
    
            echo "<div class='row'>";

            echo "<div class='col-12 mb-4'>";
            echo "<div class='card'>";
            echo "<div class='card-body'>";
            echo "<h2 class='card-title'>Cartes bancaires</h2>";
            $reqCarte = $conn->prepare("SELECT * FROM CartesBancaire C, Appartenir A WHERE C.numeroCarte=A.numeroCarte AND A.idCompte = :id");
            $reqCarte->execute(['id' => $idCompte]);
            if ($reqCarte->rowCount() > 0) {
                $cartes = $reqCarte->fetchAll();
                foreach ($cartes as $carte) {
                    $maskedNumeroCarte = str_repeat('*', strlen($carte['numeroCarte']) - 4) . substr($carte['numeroCarte'], -4);
                    $dateExpiration = date('m/y', strtotime($carte['dateExpiration']));
                    echo "<div class='d-flex justify-content-between align-items-center mb-2'>";
                    echo "<p>{$maskedNumeroCarte} {$dateExpiration} {$carte['titulaire']}</p>";
                    echo "<form method='POST' action='voirCompte.php?idCompte={$idCompte}' class='d-inline'>";
                    echo "<input type='hidden' name='supprimerCarte' value='{$carte['numeroCarte']}'>";
                    echo "<button type='submit' class='btn btn-sm' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cette carte bancaire ?\");'><i class='fas fa-trash-alt'></i></button>";
                    echo "</form>";
                    echo "</div>";
                }
            } else {
                echo "<p>Aucune carte bancaire enregistrée.</p>";
            }
            echo "</div>";
            echo "</div>";
            echo "</div>";

            echo "<div class='col-12 mb-4'>";
            echo "<div class='card'>";
            echo "<div class='card-body'>";
            echo "<h2 class='card-title'>Statistiques de l'utilisateur</h2>";
            $reqStats = $conn->prepare("SELECT COUNT(DISTINCT cmd.idCommande) as nbCommandes, COUNT(*) as nbProduits, SUM(q.nbCommandee * v.prix) AS montantTotal 
                                        FROM Commandes cmd, Quantites q, Variantes v 
                                        WHERE cmd.idCommande = q.idCommande AND q.idProduit = v.idProduit AND q.variante = v.variante AND cmd.idCompte = :idCompte AND cmd.estPanier = 0");
            $reqStats->execute(['idCompte' => $idCompte]);
            $stats = $reqStats->fetch();

            echo "<div class='row'>";
            echo "<div class='col-md-4 text-center'>";
            echo "<h3>Nombre de Commandes</h3>";
            echo "<canvas id='chart-commandes'></canvas>";
            echo "</div>";
            echo "<div class='col-md-4 text-center'>";
            echo "<h3>Produits Commandés</h3>";
            echo "<canvas id='chart-produits'></canvas>";
            echo "</div>";
            echo "<div class='col-md-4 text-center'>";
            echo "<h3>Montant Total Dépensé</h3>";
            echo "<p class='display-6 text-success'><strong>{$stats['montantTotal']} €</strong></p>";
            echo "</div>";
            echo "</div>";

            echo "</div>";
            echo "</div>";
            echo "</div>";

            echo "</div>"; // Fin du conteneur .row
    
            echo "<h2 class='text-center'>Avis sur les produits</h2>";
            echo "<div class='card mb-4'>";
            echo "<div class='card-body' style='max-height: 300px; overflow-y: auto;'>";

            $reqAvis = $conn->prepare("SELECT p.nomProduit, a.note, a.texteAvis, p.idProduit
                           FROM Avis a, Produits p 
                           WHERE a.idProduit = p.idProduit AND a.idCompte = :id");
            $reqAvis->execute(['id' => $idCompte]);
            if ($reqAvis->rowCount() > 0) {
                $avis = $reqAvis->fetchAll();
                foreach ($avis as $unAvis) {
                    echo "<div class='mb-3 p-3 bg-light border-start border-warning'>";
                    echo "<a href='detailProduit.php?idProduit={$unAvis['idProduit']}' class='text-primary fw-bold'>Produit : {$unAvis['nomProduit']}</a>";
                    echo "<div class='note'>Note : {$unAvis['note']} ★</div>";
                    if (!empty($unAvis['texteAvis'])) {
                        echo "<div class='comment'>Commentaire : {$unAvis['texteAvis']}</div>";
                    }
                    echo "</div>";
                }
            } else {
                echo "<p class='text-center'>Aucun avis laissé.</p>";
            }
            echo "</div>";
            echo "</div>";

            echo "<h2 class='text-center'>Historique des commandes</h2>";
            echo "<div class='card mb-4'>";
            echo "<div class='card-body' style='max-height: 300px; overflow-y: auto;'>";

            $reqCommandes = $conn->prepare("SELECT * FROM Commandes WHERE idCompte = :idCompte AND estPanier = 0 ORDER BY idCommande DESC");
            $reqCommandes->execute(['idCompte' => $idCompte]);
            if ($reqCommandes->rowCount() > 0) {
                $commandes = $reqCommandes->fetchAll();
                foreach ($commandes as $commande) {
                    $reqTotal = $conn->prepare("SELECT SUM(q.nbCommandee * v.prix) AS totalCommande 
                                                FROM Quantites q, Variantes v 
                                                WHERE q.idCommande = :idCommande AND q.idProduit = v.idProduit AND q.variante = v.variante");
                    $reqTotal->execute(['idCommande' => $commande['idCommande']]);
                    $total = $reqTotal->fetchColumn();

                    if ($total) {
                        echo "<div class='mb-3'>";
                        echo "<p>Commande n°{$commande['idCommande']} - Date : {$commande['dateCommande']} - Total : {$total}€ <button class='btn btn-dark btn-sm' onclick='toggleDetails({$commande['idCommande']})'>Voir Détails</button></p>";
                        echo "<div id='details-{$commande['idCommande']}' class='details' style='display:none;'>";
                        $reqDetails = $conn->prepare("SELECT * FROM Quantites q, Produits p, Variantes v WHERE q.idCommande = :idCommande AND q.idProduit = p.idProduit AND q.variante = v.variante AND v.idProduit = q.idProduit;");
                        $reqDetails->execute(['idCommande' => $commande['idCommande']]);
                        $details = $reqDetails->fetchAll();

                        foreach ($details as $detail) {
                            echo "<p>Produit : <a href='detailProduit.php?idProduit={$detail['idProduit']}&var={$detail['variante']}'>{$detail['nomProduit']}</a> - Quantité : {$detail['nbCommandee']} - Variante : {$detail['variante']} - Prix : " . ($detail['prix'] * $detail['nbCommandee']) . "€ </p>";
                        }
                        echo "</div>";
                        echo "</div>";
                    }
                }
            } else {
                echo "<p class='text-center'>Aucune commande passée.</p>";
            }
            echo "</div>";
            echo "</div>";

        } else {
            echo "<p class='text-center'>Utilisateur non trouvé.</p>";
        }
    }
    else {
        echo "<p class='text-center'>Aucun utilisateur sélectionné.</p>";
        header("location: comptes.php");
        exit();
    }
    
    ?>
</main>

<?php
$reqGlobal = $conn->prepare("SELECT COUNT(*) as totalCommandes FROM Commandes WHERE estPanier = 0");
$reqGlobal->execute();
$nbCommandesGlobales = $reqGlobal->fetchColumn();

?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const stats = {
        nbCommandes: <?php echo $stats['nbCommandes'] ?? 0; ?>,
        nbProduits: <?php echo $stats['nbProduits'] ?? 0; ?>,
        montantTotal: <?php echo $stats['montantTotal'] ?? 0; ?>,
        globalnbCommandes: <?php echo $nbCommandesGlobales ?? 0; ?>
    };

    const totalCommandesGlobales = stats.globalnbCommandes;

    // Graphique 1 : Nombre de commandes
    const ctx1 = document.getElementById('chart-commandes').getContext('2d');
    const autresCommandes = Math.max(totalCommandesGlobales - stats.nbCommandes, 0);

    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['Commandes de l’utilisateur', 'Commandes des autres utilisateurs'],
            datasets: [{
                label: 'Répartition des commandes',
                data: [stats.nbCommandes, autresCommandes],
                backgroundColor: ['#FF0000', '#ffe5e5'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                },
            }
        }
    });

    const ctx2 = document.getElementById('chart-produits').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['Produits Commandés'],
            datasets: [{
                label: 'Produits Commandés',
                data: [stats.nbProduits],
                backgroundColor: '#FF0000',
                barThickness: 20
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { display: false },
                y: { beginAtZero: true }
            }
        }
    });

    const ctx3 = document.getElementById('chart-montant').getContext('2d');
    new Chart(ctx3, {
        type: 'line',
        data: {
            labels: ['Montant Total (€)'],
            datasets: [{
                label: 'Montant Total Dépensé',
                data: [stats.montantTotal],
                backgroundColor: '#FF0000',
                borderColor: '#FF0000',
                fill: false
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { display: false },
                y: { beginAtZero: true }
            }
        }
    });

    function toggleDetails(idCommande) {
        const detailsDiv = document.getElementById('details-' + idCommande);
        if (detailsDiv.style.display === 'none') {
            detailsDiv.style.display = 'block';
        } else {
            detailsDiv.style.display = 'none';
        }
    }
</script>