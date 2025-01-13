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

$idCommande = (int) $_GET['idCommande'];

// Récupérer les détails de la commande
$queryCommande = "
    SELECT cmd.idCommande, cmd.dateCommande, SUM(q.nbCommandee * v.prix) AS montant
    FROM Commandes cmd
    JOIN Quantites q ON cmd.idCommande = q.idCommande
    JOIN Variantes v ON q.idProduit = v.idProduit AND q.variante = v.variante
    WHERE cmd.idCommande = :idCommande
    GROUP BY cmd.idCommande;
";

$stmtCommande = $conn->prepare($queryCommande);
$stmtCommande->execute(['idCommande' => $idCommande]);
$commande = $stmtCommande->fetch(PDO::FETCH_ASSOC);

// Vérifie si la commande existe
if (!$commande) {
    echo '<p class="error">Erreur : ID de commande invalide.</p>';
    exit();
}

// Système de pagination
$itemsPerPage = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;
$idCommande = $commande['idCommande'];


// Récupérer les produits de la commande avec pagination
$queryProduits = "
    SELECT p.nomProduit, q.nbCommandee, v.prix, (q.nbCommandee * v.prix) AS total, q.idProduit, q.variante
    FROM Quantites q
    JOIN Produits p ON q.idProduit = p.idProduit
    JOIN Variantes v ON q.idProduit = v.idProduit AND q.variante = v.variante
    WHERE q.idCommande = :idCommande
    LIMIT :offset, :itemsPerPage;
";

$stmtProduits = $conn->prepare($queryProduits);
$stmtProduits->bindParam(':idCommande', $idCommande, PDO::PARAM_INT);
$stmtProduits->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmtProduits->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
$stmtProduits->execute();
$produits = $stmtProduits->fetchAll(PDO::FETCH_ASSOC);

// Compte le nombre total de produits dans la commande
$countQuery = "
    SELECT COUNT(*) as total
    FROM Quantites q
    WHERE q.idCommande = :idCommande;
";
$countStmt = $conn->prepare($countQuery);
$countStmt->execute(['idCommande' => $idCommande]);
$totalProduits = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalProduits / $itemsPerPage);

$stmtCommande->closeCursor();
$stmtProduits->closeCursor();
unset($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail de la commande</title>
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
                    <a href="historiqueCommandes.php" class="back-to-home-histo">
                        ← Retour
                    </a>
                </div>
                <h1>Détail de la commande #<?php echo htmlspecialchars($commande['idCommande']); ?></h1>
                <div class="order-info">
                    <p><span>Date de commande:</span> <?php echo htmlspecialchars($commande['dateCommande']); ?></p>
                    <p><span>Montant total:</span> <?php echo htmlspecialchars(number_format($commande['montant'], 2)); ?> €</p>
                </div>
                <br><br>
                <h2>Produits commandés</h2>
                
                <table style="border: 1px solid black;">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix unitaire</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody style="border: 1px solid black; cursor: pointer;">
                        <?php foreach ($produits as $produit): ?>
                            <tr style="border: 1px solid black; cursor: pointer;" onclick="window.location.href='detailProduit.php?idProduit=<?php echo htmlspecialchars($produit['idProduit']); ?>&var=<?php echo htmlspecialchars($produit['variante']); ?>'">
                                <td style="border: 1px solid black;">
                                    <?php echo htmlspecialchars($produit['nomProduit']); ?>
                                </td>
                                <td style="border: 1px solid black;"><?php echo htmlspecialchars($produit['nbCommandee']); ?></td>
                                <td style="border: 1px solid black;"><?php echo htmlspecialchars(number_format($produit['prix'], 2)); ?> €</td>
                                <td style="border: 1px solid black;"><?php echo htmlspecialchars(number_format($produit['total'], 2)); ?> €</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <!-- Pagination -->
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?idCommande=<?php echo htmlspecialchars($commande['idCommande']); ?>&page=<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?idCommande=<?php echo htmlspecialchars($commande['idCommande']); ?>&page=<?php echo $page + 1; ?>">Page Suivante →</a>
                    <?php endif; ?>
                    <span><?php echo $page; ?> - <?php echo $totalPages; ?> / Aller à la page <input type="number" id="gotoPage" min="1" max="<?php echo $totalPages; ?>" value="<?php echo $page; ?>"> <button onclick="goToPage()">Go</button></span>
                </div>
                <br><br>
                <div class="view-status">
                    <a href="suiviCommande.php?idCommande=<?php echo htmlspecialchars($commande['idCommande']); ?>" class="btn-view-status">Voir le statut de la commande</a>
                </div>
            </div>
        </main>
    </div>

    <?php require_once './includes/footer.php'; ?>
    <script>
        // Fonction pour aller à une page spécifique
        function goToPage() {
            var page = document.getElementById('gotoPage').value;
            if (page >= 1 && page <= <?php echo $totalPages; ?>) {
                window.location.href = '?idCommande=<?php echo $commande['idCommande']; ?>&page=' + page;
            }
        }
    </script>
</body>
</html>