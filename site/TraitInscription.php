<?php 
    include('connect.inc.php');

    if(isset($_POST["valider"]) && isset($_POST["login"]) && isset($_POST["nom"]) && isset($_POST["prenom"]) && isset($_POST["email"]) && isset($_POST["numero"]) && isset($_POST["mdp"])){
        $login=htmlentities($_POST["login"]);
        $nom=htmlentities($_POST["nom"]);
        $prenom=htmlentities($_POST["prenom"]);
        $email=htmlentities($_POST["email"]);
        $numero=htmlentities($_POST["numero"]);
        $mdp=htmlentities($_POST["mdp"]);

        $motifEmail = '#^[a-zA-Z0-9.-]+@[a-zA-Z0-9.-]{2,}.[a-zA-Z]{2,4}$#' ;
        if(!preg_match($motifEmail, $email)){
            header("Location: inscription.php?msgErreur=Veuillez entrée un email valide (exemple@gmail.com)");
            exit();
        }

        $motifNumero = '#^0[1-9]\d{8}$#' ;
        if(!preg_match($motifNumero, $numero)){
            header("Location: inscription.php?msgErreur=Veuillez entrée un numéro valide");
            exit();
        }

        $testEmail=$conn->prepare("SELECT * FROM Comptes WHERE email= :mail");
        $testEmail->execute(['mail'=>$email]);

        if ($testEmail->rowCount() >= 1) {
            header("Location: inscription.php?msgErreur=Cet email est déjà utilisé");
            exit();
        }

        $testId=$conn->prepare("SELECT * FROM Comptes WHERE identifiant= :id");
        $testId->execute(['id'=>$login]);

        if ($testId->rowCount() >= 1) {
            header("Location: inscription.php?msgErreur=Cet identifiant est déjà utilisé");
            exit();
        }

        $mdp=password_hash($mdp, PASSWORD_BCRYPT);


        try{
            //Insertion du compte
            $insert=$conn->prepare("INSERT INTO Comptes (nom, prenom, email, identifiant, mdp, estAdmin, numeroTelephone) VALUES (:nom, :prenom, :email, :identifiant, :mdp, false, :numeroTelephone)");
            $insert->execute(['nom'=>$nom, 'prenom'=>$prenom, 'email'=>$email, 'identifiant'=>$login, 'mdp'=>$mdp, 'numeroTelephone'=>$numero]);

            
            try{
                //Création du panier
                $insertPanier=$conn->prepare("INSERT INTO Commandes (idCompte, estPanier) VALUES (:idCompte, true)");
                $insertPanier->execute(['idCompte'=> $conn->lastInsertId()]);
            }catch(PDOException $e){
                header("Location: inscription.php?msgErreur=Erreur lors de la création du panier");
                exit();
            }
            
            

            
            


            header("Location: FormConnexion.php?msgSucces=Inscription réussie, veuillez vous connecter");
            exit();
        }catch(PDOException $e){
            header("Location: inscription.php?msgErreur=Erreur lors de l'insertion");
            exit();
        }

    }else{
        header("Location: inscription.php?msgErreur=Veuillez remplir tout les champs");
        exit();
    }





?>