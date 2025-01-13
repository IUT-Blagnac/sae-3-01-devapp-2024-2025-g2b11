<?php
session_start();
include('connect.inc.php');

if (isset($_POST["connec"]) && isset($_POST["login"]) && isset($_POST["mdp"])) {
    $login = htmlentities($_POST["login"]);
    $mdp = htmlentities($_POST["mdp"]);

    $compte = $conn->prepare("SELECT * FROM Comptes WHERE identifiant = :login");
    $compte->execute(['login' => $login]);



    if ($compte->rowCount() == 1) {
        $row = $compte->fetch(PDO::FETCH_ASSOC);
        if (password_verify($mdp, $row['mdp'])) {
            $_SESSION['Suser'] = $row['idCompte'];
            

            if (!empty($_POST["souvenir"])) {
                setcookie("Csouvenir", $login, time() + 300);
            }

            //Récupération du panier lié au compte
            $reqPanier=$conn->prepare("SELECT * FROM Commandes WHERE idCompte=:id && estPanier=1");
            $reqPanier->execute(['id'=>$row['idCompte']]);
            if ($reqPanier->rowCount() == 1) {
                $rowP = $reqPanier->fetch(PDO::FETCH_ASSOC);
                $_SESSION['SidPanier']=$rowP['idCommande'];
            }








            //Vérification des droits du compte
            $adminCheck = $conn->prepare("SELECT estAdmin, estInactif FROM Comptes WHERE identifiant = :login");
            $adminCheck->execute(['login' => htmlentities($login)]);

            if ($adminCheck->rowCount() >= 1) {
                $verif = $adminCheck->fetch();
                if ($verif['estAdmin'] == 1) {
                    $_SESSION['Sadmin'] = true;
                }
            }

            if ($verif['estInactif'] == 1) {
                session_destroy();
                header("Location: FormConnexion.php?msgErreur=Votre compte est inactif, veuillez contacter un administrateur");
                exit();
            }
            header("Location: accueil.php");
            exit();
        } else {
            header("Location: FormConnexion.php?msgErreur=Erreur de connexion, identifiant ou mot de passe incorrect");
        }
    } else {
        header("Location: FormConnexion.php?msgErreur=Erreur de connexion, identifiant ou mot de passe incorrect");
    }

} else {
    header("Location: FormConnexion.php?msgErreur=Erreur de connexion, veuillez remplir tout les champs");
    exit();
}
?>