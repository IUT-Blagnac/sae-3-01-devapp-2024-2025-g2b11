<?php
require_once 'vendor/autoload.php';
include "connect.inc.php";

$faker = Faker\Factory::create('fr_FR');

try {

    $stmtComptes = $conn->query("SELECT idCompte, nom, prenom FROM Comptes");
    $comptes = $stmtComptes->fetchAll(PDO::FETCH_ASSOC);


    $stmtProduits = $conn->query("SELECT idProduit FROM Produits");
    $produits = $stmtProduits->fetchAll(PDO::FETCH_COLUMN);

    foreach ($comptes as $compte) {
        $idCompte = $compte['idCompte'];
        $titulaire = $compte['nom'] . ' ' . $compte['prenom'];


        $nbCartes = rand(1, 2);
        for ($i = 0; $i < $nbCartes; $i++) {
            $numeroCarte = $faker->creditCardNumber;
            $dateExpiration = $faker->creditCardExpirationDateString; 
            $dateExpiration = str_replace('/', '', $dateExpiration); 

            $stmtCarte = $conn->prepare("
                INSERT INTO CartesBancaire (numeroCarte, dateExpiration, titulaire) 
                VALUES (:numeroCarte, :dateExpiration, :titulaire)
            ");
            $stmtCarte->execute([
                ':numeroCarte' => $numeroCarte,
                ':dateExpiration' => $dateExpiration,
                ':titulaire' => $titulaire
            ]);


            $stmtAppartenir = $conn->prepare("
                INSERT INTO Appartenir (idCompte, numeroCarte) 
                VALUES (:idCompte, :numeroCarte)
            ");
            $stmtAppartenir->execute([
                ':idCompte' => $idCompte,
                ':numeroCarte' => $numeroCarte
            ]);
        }

    }

    echo "Cartes bancaires et avis générés et associés avec succès !";

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
