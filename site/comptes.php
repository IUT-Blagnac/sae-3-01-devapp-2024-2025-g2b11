<?php
session_start();
if (!isset($_SESSION['Sadmin']) or $_SESSION["Sadmin"] != true) {
    if (isset($_SESSION['Suser'])) {
        header("location: accueil.php");
        exit();
    }
    header("location: FormConnexion.php");
    exit();
}

include "connect.inc.php";

if (isset($_POST['activate_user'])) {
    $userIdToActivate = $_POST['user_id'];
    $activateQuery = $conn->prepare("UPDATE Comptes SET estInactif = 0 WHERE idCompte = :idCompte");
    $activateQuery->execute([':idCompte' => $userIdToActivate]);
    echo "<p style='color: green;'>Utilisateur activé avec succès.</p>";
}

if (isset($_POST['deactivate_user'])) {
    $userIdToDeactivate = $_POST['user_id'];
    $deactivateQuery = $conn->prepare("UPDATE Comptes SET estInactif = 1 WHERE idCompte = :idCompte");
    $deactivateQuery->execute([':idCompte' => $userIdToDeactivate]);
    echo "<p style='color: green;'>Utilisateur rendu inactif avec succès.</p>";
}

// Barre de recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Requête pour récupérer les utilisateurs (avec filtre si une recherche est effectuée)
$req = "SELECT * FROM Comptes";
if ($search) {
    $req .= " WHERE identifiant LIKE :search OR email LIKE :search OR estAdmin LIKE :search";
}
$utilisateurs = $conn->prepare($req);
if ($search) {
    $utilisateurs->execute([':search' => "%$search%"]);
} else {
    $utilisateurs->execute();
}
$userList = $utilisateurs->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les Comptes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once './includes/header.php'; ?>

    <div class="dashboard-container" >
        <aside class="sidebar">
            <nav>
                <ul>
                    <li><a href="dashboard.php">Accueil</a></li>
                    <li><a href="gestionProduits.php">Gérer les produits</a></li>
                    <li><a href="gestionCategories.php">Gérer les catégories</a></li>
                    <li><a href="comptes.php">Gérer les comptes</a></li>
                    <li><a href="communicationEquipe.php">Communiquer avec l'équipe</a></li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content"  style="width: 90%; ">
            <h1>Gestion des Utilisateurs</h1>

            <!-- Barre de recherche -->
            <form method="GET" class="search-bar" style="width: 100%; margin-right: auto;">
                <input type="text" name="search" placeholder="Rechercher un utilisateur..." value="<?php echo htmlentities($search); ?>">
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </form>

            <!-- Tableau des utilisateurs -->
            <table class="category-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom d'utilisateur</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($utilisateurs->rowCount() > 0): ?>
                        <?php foreach ($userList as $user): ?>
                            <tr>
                                <td><?php echo htmlentities($user['idCompte']); ?></td>
                                <td><?php echo htmlentities($user['identifiant']); ?></td>
                                <td><?php echo htmlentities($user['email']); ?></td>
                                <td><?php echo ($user['estAdmin'] == 1 ? "Administrateur" : "Utilisateur"); ?></td>
                                <td>
                                    <form method="GET" action="voirCompte.php?" style="display: inline;">
                                        <input type="hidden" name="idCompte" value="<?php echo $user['idCompte']; ?>">
                                        <button type="submit" class="btn btn-warning">Voir</button>
                                    </form>
                                    <?php if ($user['estInactif'] == 1): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['idCompte']; ?>">
                                            <button type="submit" name="activate_user" class="btn btn-success" onclick="return confirm('Êtes-vous sûr de vouloir rendre cet utilisateur actif ?')">Rendre actif</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['idCompte']; ?>">
                                            <button type="submit" name="deactivate_user" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir rendre cet utilisateur inactif ?')">Rendre inactif</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">Aucun utilisateur trouvé.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
    <?php include './includes/footer.php'; ?>
</body>
</html>