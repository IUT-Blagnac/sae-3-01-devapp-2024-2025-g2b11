<?php
session_start();
require_once 'connect.inc.php'; // Connexion à la base de données

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['Suser'])) {
    header('Location: login.php'); // Redirige vers la page de connexion si non connecté
    exit();
}

// Validation de l'ID de commande
if (!isset($_GET['idCommande']) || !is_numeric($_GET['idCommande'])) {
    header('Location: historiqueCommandes.php'); // Redirige si l'ID de commande est manquant ou invalide
    exit();
}

$idCommande = $_GET['idCommande'];

// Récupérer les détails de la commande
$queryCommande = "
    SELECT cmd.idCommande, cmd.dateCommande, cmd.niveauLivraison
    FROM Commandes cmd
    WHERE cmd.idCommande = :idCommande;
";

$stmtCommande = $conn->prepare($queryCommande);
$stmtCommande->execute(['idCommande' => $idCommande]);
$commande = $stmtCommande->fetch(PDO::FETCH_ASSOC);

// Vérifie si la commande existe
if (!$commande) {
    echo '<p class="error">Erreur : ID de commande invalide.</p>';
    exit();
}

$stmtCommande->closeCursor();
unset($conn);

$niveauLivraison = $commande['niveauLivraison'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi de la commande</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-content p {
            font-size: 1.1rem;
            color: var(--black);
            margin-bottom: 1rem;
            transition: color 0.3s ease;
        }

        .dashboard-content p:hover {
            color: var(--monza);
        }

        .dashboard-content .btn-view-status {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: var(--monza);
            color: var(--white);
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .dashboard-content .btn-view-status:hover {
            background-color: var(--light-red);
            transform: scale(1.05);
        }

        .dashboard-content .order-info {
            font-size: 1.2rem;
            color: var(--black);
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: var(--soft-grey);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .dashboard-content .order-info span {
            font-weight: bold;
            color: var(--monza);
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
                    <li><a href="#">Offres personnalisées</a></li>
                    <li><a href="#">Paramètres de compte</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Contenu principal -->
        <main class="dashboard-content">
            <div class="container my-5">
                <div class="top-left-link-histo">
                    <a href="detailCommande.php?idCommande=<?php echo htmlspecialchars($commande['idCommande']); ?>" class="back-to-home-histo">
                        ← Retour
                    </a>
                </div>
                <h1>Suivi de la commande #<?php echo htmlspecialchars($commande['idCommande']); ?></h1>
                <div class="order-info">
                    <p><span>Date de commande:</span> <?php echo htmlspecialchars($commande['dateCommande']); ?></p>
                </div>
                <br><br>
                <h2>Statut de la commande</h2>
                <div class="tracking-status">
                    <div class="step <?php echo ($niveauLivraison === NULL || $niveauLivraison >= 1) ? 'completed' : ''; ?>">Commande passée</div>
                    <div class="step <?php echo ($niveauLivraison >= 2) ? 'completed' : ''; ?>">En préparation</div>
                    <div class="step <?php echo ($niveauLivraison >= 3) ? 'completed' : ''; ?>">Expédiée</div>
                    <div class="step <?php echo ($niveauLivraison == 4) ? 'completed' : ''; ?>">Livrée</div>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar" style="width: <?php echo ($niveauLivraison === NULL ? 0 : ($niveauLivraison / 4) * 100); ?>%;"></div>
                </div>
            </div>
        </main>
    </div>

    <?php require_once './includes/footer.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const steps = document.querySelectorAll('.tracking-status .step');
            const progressBar = document.querySelector('.progress-bar');
            let statutLivraison = <?php echo $niveauLivraison === NULL ? 0 : $niveauLivraison; ?>;
            
            // Mettre à jour les étapes et la barre de progression
            for (let i = 0; i < statutLivraison; i++) {
                steps[i].classList.add('completed');
            }
            progressBar.style.width = `${(statutLivraison / steps.length) * 100}%`;
        });
    </script>
</body>
</html>
