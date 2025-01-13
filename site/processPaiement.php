<?php
include "connect.inc.php";

session_start();

if (!isset($_SESSION['Suser'])) {
    header('Location: FormConnexion.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modePaiement'], $_POST['idAdresse'])) {
    $idCompte = $_SESSION['Suser'];
    $idCommande = $_SESSION['SidPanier'];
    $modePaiement = $_POST['modePaiement'];
    $idAdresse = (int)$_POST['idAdresse'];
    $typePaiement = (strpos($modePaiement, 'CB-') === 0) ? 'CB' : 'PayPal';
    $datePaiement = date('Y-m-d');
    $pointsFidelite = (int)$_POST['pointsFidelite'];
    $reduction = 0;

    // Calculer la réduction en fonction des points de fidélité
    if ($pointsFidelite == 20) {
        $reduction = 5;
    } elseif ($pointsFidelite == 50) {
        $reduction = 15;
    } elseif ($pointsFidelite == 100) {
        $reduction = 35;
    }

    try {
        $conn->beginTransaction();

        // Appliquer la réduction sur le total de la commande
        $stmtTotal = $conn->prepare("
            SELECT SUM(v.prix * q.nbCommandee) AS total
            FROM Quantites q
            JOIN Variantes v ON q.idProduit = v.idProduit AND q.variante = v.variante
            WHERE q.idCommande = :idCommande
        ");
        $stmtTotal->execute([':idCommande' => $idCommande]);
        $total = $stmtTotal->fetchColumn();

        // Appliquer les réductions des variantes
        $stmtReductions = $conn->prepare("
            SELECT SUM((v.prix * q.nbCommandee) * (v.reduction / 100)) AS totalReduction
            FROM Quantites q
            JOIN Variantes v ON q.idProduit = v.idProduit AND q.variante = v.variante
            WHERE q.idCommande = :idCommande
        ");
        $stmtReductions->execute([':idCommande' => $idCommande]);
        $totalReduction = $stmtReductions->fetchColumn();

        $total -= $totalReduction;
        $total -= ($total * ($reduction / 100));

        // Calculer les points de fidélité gagnés
        $pointsGagnes = floor($total / 10);

        // Mettre à jour les points de fidélité du compte
        $stmtUpdatePoints = $conn->prepare("
            UPDATE Comptes
            SET pointF = pointF - :pointsFidelite + :pointsGagnes
            WHERE idCompte = :idCompte
        ");
        $stmtUpdatePoints->execute([
            ':pointsFidelite' => $pointsFidelite,
            ':pointsGagnes' => $pointsGagnes,
            ':idCompte' => $idCompte
        ]);

        // Mettre à jour la commande avec la promotion de fidélité
        $stmtUpdateCommande = $conn->prepare("
            UPDATE Commandes
            SET promoFidel = 1, niveauLivraison = 1
            WHERE idCommande = :idCommande
        ");
        $stmtUpdateCommande->execute([':idCommande' => $idCommande]);

        // Passer la commande
        $stmt = $conn->prepare("CALL PasserCommande2(:idCommande, :typePaiement, :datePaiement, :idAdresse)");
        $stmt->execute([
            ':idCommande' => $idCommande,
            ':typePaiement' => $typePaiement,
            ':datePaiement' => $datePaiement,
            ':idAdresse' => $idAdresse
        ]);

        unset($_SESSION['SidPanier']);

        // Créer le cookie de suivi de commande
        $suiviCommande = [
            'idCommande' => $idCommande,
            'statutLivraison' => 1
        ];
        setcookie('suiviCommande', json_encode($suiviCommande), time() + 1800, '/'); // 30 minutes

        $conn->commit();

        $_SESSION['message'] = "Le paiement a été effectué avec succès via {$typePaiement}. Merci pour votre achat.";

        header('Location: panier.php');
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        if ($e->getCode() == 'HY000') {
            $_SESSION['erreur'] = "Un produit du panier n'est plus disponible. Veuillez le retirer ou attendre qu'il soit restocké.";
            header('Location: panier.php');
        } else {
            $_SESSION['erreur'] = "Erreur lors de la validation de la commande. Veuillez réessayer.";
            header('Location: panier.php');
        }
    }
} else {
    echo "Veuillez sélectionner un mode de paiement et une adresse.";
}
?>
