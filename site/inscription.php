<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formulaire d'inscription'</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <div class="register-container">
    <div class="top-left-link">
        <a href="accueil.php" class="back-to-home">
            ← Retourner à l'accueil
        </a>
    </div>
    <div class="register-box">
        <h2>Créer un compte</h2>
        <form action="TraitInscription.php" method="post">
            <div class="register-group">
                <label for="first-name">Prénom</label>
                <input type="text" id="first-name" name="prenom" required>
            </div>
            <div class="register-group">
                <label for="last-name">Nom</label>
                <input type="text" id="last-name" name="nom" required>
            </div>
            <div class="register-group">
                <label for="username">Identifiant</label>
                <input type="text" id="username" name="login" required>
            </div>
            <div class="register-group">
                <label for="email">Adresse Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="register-group">
                <label for="phone">N° de téléphone</label>
                <input type="text" id="phone" name="numero" required>
            </div>
            <div class="register-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="mdp" required>
            </div>
            <!--
            <div class="register-group">
                <label for="confirm-password">Confirmer le mot de passe</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
            </div>
            -->
            <label><button type="submit" name="valider" value="valide" class="btn-register">S'inscrire</button></label>
            <div class="form-to-login">Vous avez déjà un compte ? Connectez-vous en cliquant <a href="FormConnexion.php">ici</a></div>
        </form>
    </div>
</div>

</body>
</html>