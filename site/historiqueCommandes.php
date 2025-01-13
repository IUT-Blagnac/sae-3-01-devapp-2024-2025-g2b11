<?php
session_start();
require_once 'connect.inc.php'; // Connexion à la base de données

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['Suser'])) {
    header('Location: login.php'); // Redirige vers la page de connexion si non connecté
    exit();
}

$idCompte = $_SESSION['Suser'];

// Systeme de pagination
$itemsPerPage = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// Récupérer l'historique des commandes de l'utilisateur avec pagination
$query = "
    SELECT cmd.idCommande, cmd.dateCommande, SUM(q.nbCommandee * v.prix) AS montant
    FROM Commandes cmd
    JOIN Quantites q ON cmd.idCommande = q.idCommande
    JOIN Variantes v ON q.idProduit = v.idProduit AND q.variante = v.variante
    WHERE cmd.idCompte = :idCompte AND cmd.estPanier = 0
    GROUP BY cmd.idCommande
    ORDER BY cmd.dateCommande DESC, cmd.idCommande DESC
    LIMIT :offset, :itemsPerPage;
";

$stmt = $conn->prepare($query);
$stmt->bindParam(':idCompte', $idCompte, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
$stmt->execute();
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Compte le nombre total de commandes
$countQuery = "
    SELECT COUNT(*) as total
    FROM (
        SELECT cmd.idCommande
        FROM Commandes cmd
        WHERE cmd.idCompte = :idCompte AND cmd.estPanier = 0
        GROUP BY cmd.idCommande
    ) as subquery;
";
$countStmt = $conn->prepare($countQuery);
$countStmt->execute(['idCompte' => $idCompte]);
$totalCommandes = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalCommandes / $itemsPerPage);

$stmt->closeCursor();
unset($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des commandes</title>
    <link rel="stylesheet" href="style.css">
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
            <div class="top-left-link-histo">
                <a href="monCompte.php" class="back-to-home-histo">
                    ← Mon compte
                </a>
            </div>
            <br><br>
            <h1>Historique des commandes</h1>
            <section>
                <?php if (empty($commandes)): ?>
                    <p>Vous n'avez passé aucune commande.</p>
                <?php else: ?>
                    <table style="border: 1px solid black;">
                        <thead>
                            <tr>
                                <th>ID Commande</th>
                                <th>Date</th>
                                <th>Montant</th>
                            </tr>
                        </thead>
                        <tbody style="border: 1px solid black;">
                            <?php foreach ($commandes as $commande): ?>
                                <tr style="border: 1px solid black; cursor: pointer;" onclick="window.location.href='detailCommande.php?idCommande=<?php echo htmlspecialchars($commande['idCommande']); ?>'">
                                    <td style="border: 1px solid black;">
                                        <?php echo htmlspecialchars($commande['idCommande']); ?>
                                    </td>
                                    <td style="border: 1px solid black;"><?php echo htmlspecialchars($commande['dateCommande']); ?></td>
                                    <td style="border: 1px solid black;"><?php echo htmlspecialchars(number_format($commande['montant'], 2)); ?> €</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!-- Pagination -->
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>">← Page Précédente</a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>">Page Suivante →</a>
                        <?php endif; ?>
                        <span><?php echo $page; ?> - <?php echo $totalPages; ?> / Aller à la page <input type="number" id="gotoPage" min="1" max="<?php echo $totalPages; ?>" value="<?php echo $page; ?>"> <button onclick="goToPage()">Go</button></span>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
    <?php require_once './includes/footer.php'; ?>
    <script>
        // Fonction pour aller à une page spécifique
        function goToPage() {
            var page = document.getElementById('gotoPage').value;
            if (page >= 1 && page <= <?php echo $totalPages; ?>) {
                window.location.href = '?page=' + page;
            }
        }
    </script>
</body>
</html>

