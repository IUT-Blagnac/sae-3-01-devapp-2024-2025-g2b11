<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formulaire de Connexion</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    
    <div class="login-container"  style="position: relative; width: 100%; height: 100vh;"> 
        <div class="top-left-link">
            <a href="accueil.php" class="back-to-home">
                ← Retourner à l'accueil
            </a>
        </div>
        <div class="login-box">
            <h2>Connexion</h2>
            <form action="TraitConnexion.php" method="POST">
                <?php
                    if(isset($_GET["msgErreur"])){ 
                        echo "<div class='error-msg'>" . htmlspecialchars($_GET["msgErreur"]) . "</div>";
                    }

                    $loginValue = isset($_COOKIE['Csouvenir']) ? $_COOKIE['Csouvenir'] : ''; 
                ?>
                <div class="form-group">
                    <label for="login">Identifiant:</label>
                    <input type="text" id="login" name="login" value="<?php echo htmlspecialchars($loginValue); ?>">
                </div>
                <div class="form-group">
                    <label for="mdp">Mot de passe:</label>
                    <input type="password" id="mdp" name="mdp">
                </div>
                <div class="form-group checkbox-group">
                    <label for="souvenir">
                        <input type="checkbox" id="souvenir" name="souvenir"> Se souvenir de moi
                    </label>
                </div>
                
                <div class="form-group form-group-flex">
                    <input type="submit" name="connec" value="Se connecter" class="btn-login">
                    <div class="form-to-register">Vous n'avez pas de compte ? Créez en un en cliquant <a href="inscription.php">ici</a> </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

