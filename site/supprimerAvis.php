<?php
session_start();
include "connect.inc.php";
if (isset($_POST['idProduit']) && isset($_POST['idCompteAvis']) && ((isset($_SESSION['Sadmin']) && $_SESSION['Sadmin'] == true) || (isset($_SESSION['Suser']) && $_SESSION['Suser'] == $_POST['idCompteAvis']))) {
    $idCompte = (int)htmlentities($_POST['idCompteAvis']);
    $idProduit = (int)htmlentities($_POST['idProduit']);

    $reqsuppAvis = $conn->prepare("DELETE FROM Avis WHERE idCompte = :idCompte AND idProduit = :idProduit");
    $reqsuppAvis->execute(['idCompte' => $idCompte, 'idProduit' => $idProduit]);

    // if ($reqsuppAvis->rowCount() > 0) {
    //     $_SESSION['message'] = "Avis supprimé avec succès.";
    // } else {
    //     $_SESSION['message'] = "Erreur lors de la suppression de l'avis.";
    // }
} else {
    // $_SESSION['message'] = "Action non autorisée.";
}

header("Location: detailProduit.php?idProduit=" . (int)$_POST['idProduit']);
exit();
?>
