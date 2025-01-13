<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Assistance</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once './includes/header.php'; ?>

    <main class="assistance-container">
        <div class="assistance-content">
            <h1>Assistance</h1>
            <form action="traitAssistance.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="motif">Motif :</label>
                    <select name="motif" id="motif" style="color:black; background-color:white;" required>
                        <option value="bug">Question/FAQ</option>
                        <option value="mise_a_jour">Feedback</option>
                        <option value="maintenance">Signaler un bug/problème</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="message">Message :</label>
                    <textarea name="message" id="message" class="form-control scrollable" rows="5" required></textarea>
                </div>
                <div class="form-group">
                    <label for="screenshot">Capture d'écran (optionnel) :</label>
                    <input type="file" name="screenshot" id="screenshot" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </form>
        </div>
    </main>
    <?php include './includes/footer.php'; ?>
    <script>
        document.getElementById('screenshot').addEventListener('change', function() {
            const fileName = this.files[0] ? this.files[0].name : 'Aucun fichier choisi';
            document.getElementById('file-name').textContent = fileName;
        });
    </script>
</body>
</html>
