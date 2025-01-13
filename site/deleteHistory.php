<?php
session_start();

if (isset($_SESSION['Suser']) && isset($_GET['productId'])) {
    $userId = $_SESSION['Suser'];
    $productId = $_GET['productId'];

    $historyData = isset($_COOKIE['historique']) ? json_decode($_COOKIE['historique'], true) : [];
    if (isset($historyData[$userId][$productId])) {
        unset($historyData[$userId][$productId]);
        setcookie('historique', json_encode($historyData), time() + 3600, '/');
    }
}
?>
