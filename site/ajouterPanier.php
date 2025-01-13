<?php
session_start();
include "connect.inc.php";

if (!isset($_SESSION['Suser'])) {
    header('Location: FormConnexion.php');
    exit();
}

if (isset($_POST['ajouterPanier'])) {
    $idProduit = (int) $_POST['idProduit'];
    $variante = $_POST['variante'];
    $idCompte = $_SESSION['Suser'];

    $reqPanier = $conn->prepare("SELECT idCommande FROM Commandes WHERE idCompte = :idCompte AND estPanier = 1");
    $reqPanier->execute(['idCompte' => $idCompte]);
    $panier = $reqPanier->fetch();

    if (!$panier) {
        $stmtNewPanier = $conn->prepare("INSERT INTO Commandes (idCompte, estPanier) VALUES (:idCompte, 1)");
        $stmtNewPanier->execute(['idCompte' => $idCompte]);
        $idCommande = $conn->lastInsertId();
    } else {
        $idCommande = $panier['idCommande'];
    }

    $reqQuantite = $conn->prepare("
        SELECT * FROM Quantites 
        WHERE idCommande = :idCommande AND idProduit = :idProduit AND variante = :variante
    ");
    $reqQuantite->execute([
        'idCommande' => $idCommande,
        'idProduit' => $idProduit,
        'variante' => $variante
    ]);

    if ($reqQuantite->rowCount() > 0) {
        $stmtUpdateQuantite = $conn->prepare("
            UPDATE Quantites 
            SET nbCommandee = nbCommandee + 1 
            WHERE idCommande = :idCommande AND idProduit = :idProduit AND variante = :variante
        ");
        $stmtUpdateQuantite->execute([
            'idCommande' => $idCommande,
            'idProduit' => $idProduit,
            'variante' => $variante
        ]);
    } else {
        $stmtInsertQuantite = $conn->prepare("
            INSERT INTO Quantites (idCommande, idProduit, variante, nbCommandee, estMontee) 
            VALUES (:idCommande, :idProduit, :variante, 1, 0)
        ");
        $stmtInsertQuantite->execute([
            'idCommande' => $idCommande,
            'idProduit' => $idProduit,
            'variante' => $variante
        ]);
    }
	
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
