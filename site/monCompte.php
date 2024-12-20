<?php
session_start();
require_once 'connect.inc.php'; // Connexion à la base de données

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['Suser'])) {
    header('Location: login.php'); // Redirige vers la page de connexion si non connecté
    exit();
}

$idCompte = $_SESSION['Suser'];

// Requête pour récupérer les informations personnelles de l'utilisateur
$query = "
    SELECT c.prenom, c.nom, c.email, c.numeroTelephone,
           a.adresse, a.codePostal, a.pays
    FROM Comptes c
    LEFT JOIN Resider r ON c.idCompte = r.idCompte
    LEFT JOIN Adresses a ON r.idAdresse = a.idAdresse
    WHERE c.idCompte = :idCompte
";

$stmt = $conn->prepare($query);
$stmt->execute(['idCompte' => $idCompte]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Requête pour récupérer les points de fidélité (exemple fictif)
$query_points = "
    SELECT SUM(nbCommandee) AS pointsFidelite
    FROM Quantites q
    JOIN Commandes cmd ON q.idCommande = cmd.idCommande
    WHERE cmd.idCompte = :idCompte AND cmd.estPanier = 0
";

$stmt_points = $conn->prepare($query_points);
$stmt_points->execute(['idCompte' => $idCompte]);
$result_points = $stmt_points->fetch(PDO::FETCH_ASSOC);
$points_fidelite = $result_points['pointsFidelite'] ?? 0;
$seuil_points = 1000; // Seuil de fidélité par défaut

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require_once './includes/header.php'; ?>

    <div class="dashboard-container">
        <!-- Menu latéral -->
        <aside class="sidebar">
            <nav>
                <ul>
                    <li><a href="#">Accueil</a></li>
                    <li><a href="#">Modifier mes informations</a></li>
                    <li><a href="#">Historique des commandes</a></li>
                    <li><a href="#">Offres personnalisées</a></li>
                    <li><a href="#">Paramètres de compte</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Contenu principal -->
        <main class="dashboard-content">
            <h1>Mon Compte</h1>

            <!-- Informations personnelles -->
            <section>
                <h2>Informations personnelles</h2>
                <p><strong>Prénom :</strong> <?= htmlspecialchars($user['prenom']) ?></p>
                <p><strong>Nom :</strong> <?= htmlspecialchars($user['nom']) ?></p>
                <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Adresse :</strong> <?= htmlspecialchars($user['adresse'] ?? 'Non définie') ?></p>
                <p><strong>Code postal :</strong> <?= htmlspecialchars($user['codePostal'] ?? 'Non défini') ?></p>
                <p><strong>Pays :</strong> <?= htmlspecialchars($user['pays'] ?? 'Non défini') ?></p>
                <p><strong>Téléphone :</strong> <?= htmlspecialchars($user['numeroTelephone']) ?></p>
            </section>

            <!-- Programme de fidélité -->
            <section>
                <h2>Programme de fidélité</h2>
                <p><strong>Points :</strong> <?= htmlspecialchars($points_fidelite) ?> / <?= htmlspecialchars($seuil_points) ?></p>
                <p><strong>Prochaine récompense :</strong> <?= htmlspecialchars($seuil_points - $points_fidelite) ?> points restants</p>
            </section>
        </main>
    </div>

    <?php include './includes/footer.php'; ?>
</body>
</html>
