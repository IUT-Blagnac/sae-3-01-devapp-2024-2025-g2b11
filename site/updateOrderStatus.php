<?php
include "connect.inc.php";

set_time_limit(0); // Allow the script to run indefinitely

while (true) {
    // Incrémenter le niveau de livraison pour les commandes dont le niveau est entre 1 et 3
    $stmtUpdate = $conn->prepare("
        UPDATE Commandes
        SET niveauLivraison = niveauLivraison + 1
        WHERE niveauLivraison IS NOT NULL AND niveauLivraison BETWEEN 1 AND 3
    ");
    $stmtUpdate->execute();

    // Sleep for 3 minutes (180 seconds)
    sleep(180);
}
?>