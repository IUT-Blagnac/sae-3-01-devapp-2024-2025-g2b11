<?php
session_start();
include "connect.inc.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['Suser']) || !isset($_SESSION['SidPanier'])) {
    header('Location: panier.php');
    exit();
}

$idCommande = $_SESSION['SidPanier'];

// Fonction pour mettre à jour le statut de livraison dans le cookie
function updateOrderStatusCookie($idCommande, $statutLivraison) {
    $suiviCommande = [
        'idCommande' => $idCommande,
        'statutLivraison' => $statutLivraison
    ];
    setcookie('suiviCommande', json_encode($suiviCommande), time() + 1800, '/'); // 30 minutes
}

// Fonction pour mettre à jour le statut de livraison dans la base de données
function updateOrderStatusDB($conn, $idCommande, $statutLivraison) {
    $stmtUpdate = $conn->prepare("
        UPDATE Commandes
        SET niveauLivraison = :statutLivraison
        WHERE idCommande = :idCommande
    ");
    $stmtUpdate->execute([
        ':statutLivraison' => $statutLivraison,
        ':idCommande' => $idCommande
    ]);
}

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
    updateOrderStatusCookie($idCommande, 1); // Réinitialiser le statut de livraison à 1
    updateOrderStatusDB($conn, $idCommande, 1); // Réinitialiser le statut de livraison à 1
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
    updateOrderStatusCookie($idCommande, 1); // Réinitialiser le statut de livraison à 1
    updateOrderStatusDB($conn, $idCommande, 1); // Réinitialiser le statut de livraison à 1
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
    updateOrderStatusCookie($idCommande, 1); // Réinitialiser le statut de livraison à 1
    updateOrderStatusDB($conn, $idCommande, 1); // Réinitialiser le statut de livraison à 1
    header('Location: panier.php');
    exit();
}

// Si aucun cas n'est trouvé, retourner au panier
header('Location: panier.php');
exit();
