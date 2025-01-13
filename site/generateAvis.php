<?php
require_once 'vendor/autoload.php';
include "connect.inc.php";

$faker = Faker\Factory::create('fr_FR');

try {
    // Vider la table Avis
    $conn->exec("DELETE FROM Avis");

    // Récupérer tous les comptes existants
    $stmtComptes = $conn->query("SELECT idCompte FROM Comptes");
    $comptes = $stmtComptes->fetchAll(PDO::FETCH_COLUMN);

    // Récupérer tous les produits existants
    $stmtProduits = $conn->query("SELECT * FROM Produits");
    $produits = $stmtProduits->fetchAll(PDO::FETCH_ASSOC);

    foreach ($comptes as $idCompte) {
        // Générer 1 ou 2 avis pour chaque compte
        $nbAvis = rand(1, 2);
        for ($i = 0; $i < $nbAvis; $i++) {
            // Sélectionner un produit aléatoire dans la liste des produits
            $produit = $faker->randomElement($produits);
            $idProduit = $produit['idProduit']; // Récupérer l'ID du produit

            // Générer un texte d'avis aléatoire (ou parfois null)
            $texteAvis =  null;

            // Générer une note aléatoire entre 1 et 10
            $note = rand(5, 10);

            // Générer une réponse uniquement si un texte d'avis existe
            $reponse = null;

            // Insérer l'avis dans la base de données
            $stmtAvis = $conn->prepare("
                INSERT INTO Avis (idProduit, idCompte, texteAvis, note, reponse) 
                VALUES (:idProduit, :idCompte, :texteAvis, :note, :reponse)
            ");
            $stmtAvis->execute([
                ':idProduit' => $idProduit,
                ':idCompte' => $idCompte,
                ':texteAvis' => $texteAvis,
                ':note' => $note,
                ':reponse' => $reponse
            ]);
        }
    }

    echo "Tous les avis ont été supprimés et de nouveaux avis ont été générés avec succès !";

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
