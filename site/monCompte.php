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
           a.adresse, a.codePostal, a.ville, a.pays,
           c.pointF
    FROM Comptes c
    LEFT JOIN Resider r ON c.idCompte = r.idCompte
    LEFT JOIN Adresses a ON r.idAdresse = a.idAdresse
    WHERE c.idCompte = :idCompte
";

$stmt = $conn->prepare($query);
$stmt->execute(['idCompte' => $idCompte]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Points de fidélité de l'utilisateur
$pointF = $user['pointF'];

// Calcul des remises disponibles
$remisesDisponibles = [];
if ($pointF >= 20) {
    $remisesDisponibles[] = "20 points = -5%";
}
if ($pointF >= 50) {
    $remisesDisponibles[] = "50 points = -15%";
}
if ($pointF >= 100) {
    $remisesDisponibles[] = "100 points = -35%";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-content h2{
            text-align: left;
        }
    </style>
</head>

<body>
    <?php require_once './includes/header.php'; ?>

    <div class="dashboard-container">
        <!-- Menu latéral -->
        <aside class="sidebar">
            <nav>
                <ul>
                    <li><a href="monCompte.php">Accueil</a></li>
                    <li><a href="modifMesinfos.php">Modifier mes informations</a></li>
                    <li><a href="historiqueCommandes.php">Historique des commandes</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Contenu principal -->
        <main class="dashboard-content">
            <h1 style="align-content: center;">Mon Compte</h1>

            <!-- Informations personnelles -->
            <section>
                <h2>Informations personnelles</h2>
                <p><strong>Prénom :</strong> <?= htmlspecialchars($user['prenom']) ?></p>
                <p><strong>Nom :</strong> <?= htmlspecialchars($user['nom']) ?></p>
                <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Adresse :</strong> <?= htmlspecialchars($user['adresse'] ?? 'Non définie') ?></p>
                <p><strong>Code postal :</strong> <?= htmlspecialchars($user['codePostal'] ?? 'Non défini') ?></p>
                <p><strong>Ville :</strong> <?= htmlspecialchars($user['ville'] ?? 'Non définie') ?></p>
                <p><strong>Pays :</strong> <?= htmlspecialchars($user['pays'] ?? 'Non défini') ?></p>
                <p><strong>Téléphone :</strong> <?= htmlspecialchars($user['numeroTelephone']) ?></p>
            </section>

            <!-- Programme de fidélité -->
            <section>
                <h2>Programme de fidélité</h2>
                <p><strong>Points de fidélité :</strong> <?= htmlspecialchars($pointF) ?> / 100</p>
                <h3>Remises disponibles :</h3>
                <ul>
                    <?php if (empty($remisesDisponibles)): ?>
                        <li>Aucune remise disponible</li>
                    <?php else: ?>
                        <?php foreach ($remisesDisponibles as $remise): ?>
                            <li><?= htmlspecialchars($remise) ?></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </section>
        </main>
    </div>

    <?php include './includes/footer.php'; ?>
</body>
</html>
