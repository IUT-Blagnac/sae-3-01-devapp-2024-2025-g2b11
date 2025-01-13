<?php
session_start();

// Vérifie si l'utilisateur est connecté et si les données nécessaires sont présentes
if (isset($_SESSION['Suser']) && isset($_GET['productId']) && isset($_GET['productName'])) {
    $productId = htmlspecialchars($_GET['productId']);
    $productName = htmlspecialchars($_GET['productName']);

    // Initialise l'historique de recherche si ce n'est pas encore fait
    if (!isset($_SESSION['search_history'])) {
        $_SESSION['search_history'] = [];
    }

    // Ajoute ou met à jour l'entrée pour le produit dans l'historique
    if (!isset($_SESSION['search_history'][$productId])) {
        $_SESSION['search_history'][$productId] = [
            'name' => $productName,
            'count' => 0,
        ];
    }

    // Incrémente le compteur d'accès pour ce produit
    $_SESSION['search_history'][$productId]['count']++;
    
    // Renvoie une réponse JSON indiquant le succès
    echo json_encode(['status' => 'success', 'message' => 'Historique mis à jour avec succès']);
} else {
    // Renvoie une réponse JSON indiquant une erreur si les paramètres sont manquants
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Paramètres invalides']);
}
?>
