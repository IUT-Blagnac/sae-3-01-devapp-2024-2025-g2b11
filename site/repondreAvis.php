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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idCompteAvis'], $_POST['idProduit'], $_POST['reponse'])) {
    $idCompte = (int) htmlentities($_POST['idCompteAvis']);
    $idProduit = (int) htmlentities($_POST['idProduit']);
    $reponse = htmlentities($_POST['reponse']);



    $reqreponse = $conn->prepare("UPDATE Avis SET reponse = :reponse WHERE idCompte = :idCompte AND idProduit = :idProduit");
    $reqreponse->execute(['idCompte' => $idCompte, 'idProduit' => $idProduit, 'reponse' => $reponse]);


    header("Location: detailProduit.php?idProduit={$idProduit}");
    exit();



} else {
    header('Location: index.php');
    exit();
}
?>