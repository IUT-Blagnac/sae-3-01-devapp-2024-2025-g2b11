<?php
session_start();
include "connect.inc.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['Suser']) || !isset($_SESSION['SidPanier'])) {
    header('Location: panier.php');
    exit();
}

$idCommande = $_SESSION['SidPanier'];

// Vérifier si une action est envoyée
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
    // Diminuer la quantité
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
    // Augmenter la quantité
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

// Si aucun cas n'est trouvé, retourner au panier
header('Location: panier.php');
exit();
