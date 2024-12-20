<?php
session_start();
include "connect.inc.php";

// V�rifier si l'utilisateur est connect�
if (!isset($_SESSION['Suser']) || !isset($_SESSION['SidPanier'])) {
    header('Location: panier.php');
    exit();
}

$idCommande = $_SESSION['SidPanier'];

// V�rifier si une action est envoy�e
if (isset($_POST['delete'])) {
    // Supprimer le produit
    [$idProduit, $variante] = explode('-', $_POST['delete']);
    $stmtDelete = $conn->prepare("
        DELETE FROM Quantites
        WHERE idCommande = :idCommande AND idProduit = :idProduit AND variante = :variante
    ");
    $stmtDelete->execute([
        ':idCommande' => $idCommande,
        ':idProduit' => $idProduit,
        ':variante' => $variante,
    ]);
    header('Location: panier.php');
    exit();
}

if (isset($_POST['decrease'])) {
    // Diminuer la quantit�
    [$idProduit, $variante] = explode('-', $_POST['decrease']);
    $stmtUpdate = $conn->prepare("
        UPDATE Quantites
        SET nbCommandee = GREATEST(nbCommandee - 1, 1)
        WHERE idCommande = :idCommande AND idProduit = :idProduit AND variante = :variante
    ");
    $stmtUpdate->execute([
        ':idCommande' => $idCommande,
        ':idProduit' => $idProduit,
        ':variante' => $variante,
    ]);
    header('Location: panier.php');
    exit();
}

if (isset($_POST['increase'])) {
    // Augmenter la quantit�
    [$idProduit, $variante] = explode('-', $_POST['increase']);
    $stmtUpdate = $conn->prepare("
        UPDATE Quantites
        SET nbCommandee = nbCommandee + 1
        WHERE idCommande = :idCommande AND idProduit = :idProduit AND variante = :variante
    ");
    $stmtUpdate->execute([
        ':idCommande' => $idCommande,
        ':idProduit' => $idProduit,
        ':variante' => $variante,
    ]);
    header('Location: panier.php');
    exit();
}

// Si aucun cas n'est trouv�, retourner au panier
header('Location: panier.php');
exit();
