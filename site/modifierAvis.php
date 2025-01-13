<?php
session_start();
include "connect.inc.php";
if (isset($_POST['idProduit']) && isset($_POST['idCompteAvis']) && ((isset($_SESSION['Sadmin']) && $_SESSION['Sadmin'] == true) || (isset($_SESSION['Suser']) && $_SESSION['Suser'] == $_POST['idCompteAvis']))) {
    $idCompte = htmlentities($_POST['idCompteAvis']);
    $idProduit = (int)htmlentities($_POST['idProduit']);
    $note = (int)htmlentities($_POST['note']);
    $texteAvis = isset($_POST['texteAvis']) ? htmlentities($_POST['texteAvis']) : "";

    $reqmodifAvis = $conn->prepare("UPDATE Avis SET texteAvis = :texteAvis , note = :note WHERE idCompte = :idCompte AND idProduit = :idProduit");
    $reqmodifAvis->execute(['idCompte' => $idCompte, 'idProduit' => $idProduit, 'texteAvis' => $texteAvis, 'note' => $note]);
    
} 
header("Location: detailProduit.php?idProduit=" . (int)$_POST['idProduit']);
exit();
?>
