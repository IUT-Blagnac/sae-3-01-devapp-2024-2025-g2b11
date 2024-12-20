<?php
include 'connect.inc.php';
// Plus d'information ici : https://fakerphp.github.io/
// Dans le répertoire où on met ce script, il faut lancer, depuis la console SSH : composer require fakerphp/faker
// Cela installe un répertoire vendor avec le package fakerphp, on peut désormais l'utiliser 
require_once 'vendor/autoload.php'; // pour charger automatiquement les classes du Vendor dans notre script PHP
// on crée un objet $faker 'version française' (pour les noms de famille, ...)
$faker = Faker\Factory::create('fr_FR');
// on génére un exemple nom 'français'

if (isset($_GET["commande"]) && $_GET['commande'] == "Ajouter2") {
    $stmt = $conn->prepare("INSERT INTO Comptes(nom, prenom, email, identifiant, mdp, estAdmin, numeroTelephone) 
    VALUES (:nom, :prenom, :email, :identifiant, :mdp, :estAdmin, :numeroTelephone)");

    $insertPanier = $conn->prepare("INSERT INTO Commandes (idCompte, estPanier) VALUES (:idCompte, true)");
        
    $stmt->execute([
        ':nom' => "admin",
        ':prenom' => "admin",
        ':email' => "admin@gmail.com",
        ':identifiant' => "admin",
        ':mdp' => password_hash("admin", PASSWORD_BCRYPT),
        ':estAdmin' => 1,
        ':numeroTelephone' => "0674484714"
    ]);

    $insertPanier->execute(['idCompte' => $conn->lastInsertId()]);


    $stmt->execute([
        ':nom' => "client",
        ':prenom' => "client",
        ':email' => "client@gmail.com",
        ':identifiant' => "client",
        ':mdp' => password_hash("client", PASSWORD_BCRYPT),
        ':estAdmin' => 0,
        ':numeroTelephone' => "0674484715"
    ]);

    $insertPanier->execute(['idCompte' => $conn->lastInsertId()]);
    echo "2 personnes ajoutées avec succès !";

}

if (isset($_GET["commande"]) && $_GET['commande'] == "AjouterPersonne") {
    $pNom = $faker->lastName;
    $pPrenom = $faker->firstName;
    $pEmail = $faker->email;
    $pLogin = $faker->userName;
    $pmdp = $faker->password;
    $tel = '06' . rand(min: 10000000, max: 99999999);

    echo "<strong>Nom:</strong> $pNom<br>";
    echo "<strong>Prénom:</strong> $pPrenom<br>";
    echo "<strong>Email:</strong> $pEmail<br>";
    echo "<strong>Identifiant:</strong> $pLogin<br>";
    echo "<strong>Mot de passe:</strong> $pmdp<br>";
    echo "<strong>Téléphone:</strong> $tel<br>";

    $stmt = $conn->prepare("INSERT INTO Comptes(nom, prenom, email, identifiant, mdp, estAdmin, numeroTelephone) 
                            VALUES (:nom, :prenom, :email, :identifiant, :mdp, :estAdmin, :numeroTelephone)");
    
    $stmt->execute([
        ':nom' => $pNom,
        ':prenom' => $pPrenom,
        ':email' => $pEmail,
        ':identifiant' => $pLogin,
        ':mdp' => password_hash($pmdp, PASSWORD_BCRYPT),
        ':estAdmin' => 0,
        ':numeroTelephone' => $tel
    ]);

    $idPersonne = $conn->lastInsertId();

    $pays = 'France';
    $codePostal = $faker->postcode;
    $adresse = random_int(1, 280) . " " . $faker->streetName;
    $numeroBatiment = $faker->buildingNumber;
    $numeroAppartement = $faker->numberBetween(1, 100);

    echo "<strong>Pays:</strong> $pays<br>";
    echo "<strong>Code Postal:</strong> $codePostal<br>";
    echo "<strong>Adresse:</strong> $adresse<br>";
    echo "<strong>Numéro de Bâtiment:</strong> $numeroBatiment<br>";
    echo "<strong>Numéro d'Appartement:</strong> $numeroAppartement<br>";

    $stmtAdresse = $conn->prepare("INSERT INTO Adresses(pays, codePostal, adresse, numeroBatiment, numeroAppartement) 
                                   VALUES (:pays, :codePostal, :adresse, :numeroBatiment, :numeroAppartement)");
    $stmtAdresse->execute([
        ':pays' => $pays,
        ':codePostal' => $codePostal,
        ':adresse' => $adresse,
        ':numeroBatiment' => $numeroBatiment,
        ':numeroAppartement' => $numeroAppartement
    ]);

    $idAdresse = $conn->lastInsertId();

    $stmtResider = $conn->prepare("INSERT INTO Resider(idCompte, idAdresse) VALUES (:idPersonne, :idAdresse)");
    $stmtResider->execute([
        ':idPersonne' => $idPersonne,
        ':idAdresse' => $idAdresse
    ]);

    $insertPanier = $conn->prepare("INSERT INTO Commandes (idCompte, estPanier) VALUES (:idCompte, true)");
    $insertPanier->execute(['idCompte' => $idPersonne]);
    echo $idAdresse . " - " . $idPersonne;
    echo "<br>Personne avec Adresse ajoutée avec succès !";
}
?>