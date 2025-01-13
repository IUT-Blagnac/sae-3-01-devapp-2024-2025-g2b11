<?php
include "connect.inc.php";

session_start();

if (!isset($_SESSION['Suser'])) {
    header('Location: FormConnexion.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['note'], $_POST['idProduit'])) {
    $idCompte = htmlentities($_SESSION['Suser']);
    $idProduit = (int)htmlentities($_POST['idProduit']);
    $note = (int)htmlentities($_POST['note']);
    $texteAvis = isset($_POST['texteAvis']) ? htmlentities($_POST['texteAvis']) : "";


    $stmt = $conn->prepare("INSERT INTO Avis (idCompte, idProduit, note, texteAvis) VALUES (:idCompte, :idProduit, :note, :texteAvis)");
    $stmt->execute(['idCompte' => $idCompte, 'idProduit' => $idProduit, 'note' => $note, 'texteAvis' => $texteAvis]);
    header("Location: detailProduit.php?idProduit={$idProduit}");
    exit();
    
}  else {
    header('Location: index.php');
    exit();
}