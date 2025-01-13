<link rel="stylesheet" href="./css/paiement.css">
<?php
require_once './includes/head.php';
require_once './includes/header.php';
include "connect.inc.php";

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['Suser'])) {
    header('Location: FormConnexion.php');
    exit();
}

$idCompte = $_SESSION['Suser'];

// Récupérer les cartes bancaires associées au compte
$stmtCartes = $conn->prepare("
    SELECT cb.numeroCarte, cb.dateExpiration, cb.titulaire
    FROM CartesBancaire cb
    JOIN Appartenir a ON cb.numeroCarte = a.numeroCarte
    WHERE a.idCompte = :idCompte
");
$stmtCartes->execute([':idCompte' => $idCompte]);
$cartes = $stmtCartes->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les adresses associées au compte
$stmtAdresses = $conn->prepare("
    SELECT a.idAdresse, a.pays, a.codePostal, a.adresse, a.ville, a.numeroBatiment, a.numeroAppartement
    FROM Adresses a
    JOIN Resider r ON a.idAdresse = r.idAdresse
    WHERE r.idCompte = :idCompte
");
$stmtAdresses->execute([':idCompte' => $idCompte]);
$adresses = $stmtAdresses->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les points de fidélité
$stmtPoints = $conn->prepare("SELECT pointF FROM Comptes WHERE idCompte = :idCompte");
$stmtPoints->execute([':idCompte' => $idCompte]);
$pointF = $stmtPoints->fetchColumn();

// Récupérer le total de la commande
$stmtTotal = $conn->prepare("
    SELECT SUM(v.prix * q.nbCommandee) AS total
    FROM Quantites q
    JOIN Variantes v ON q.idProduit = v.idProduit AND q.variante = v.variante
    WHERE q.idCommande = :idCommande
");
$stmtTotal->execute([':idCommande' => $_SESSION['SidPanier']]);
$total = $stmtTotal->fetchColumn();

// Récupérer les réductions des variantes
$stmtReductions = $conn->prepare("
    SELECT SUM((v.prix * q.nbCommandee) * (v.reduction / 100)) AS totalReduction
    FROM Quantites q
    JOIN Variantes v ON q.idProduit = v.idProduit AND q.variante = v.variante
    WHERE q.idCommande = :idCommande
");
$stmtReductions->execute([':idCommande' => $_SESSION['SidPanier']]);
$totalReduction = $stmtReductions->fetchColumn();

$total -= $totalReduction;

// Affichage des messages de succès ou d'erreur
$message = isset($_SESSION['message']) ? $_SESSION['message'] : null;
unset($_SESSION['message']); // Supprimez le message après affichage
?>

<main class="container mt-5">
    <h1>Paiement</h1>
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form action="processPaiement.php" method="POST">
        <!-- Section pour la sélection des adresses -->
        <div class="address-options mb-4">
            <h2>Vos Adresses</h2>
            <?php foreach ($adresses as $adresse): ?>
                <div>
                    <label>
                        <input type="radio" name="idAdresse" value="<?php echo htmlentities($adresse['idAdresse']); ?>" required>
                        <?php echo htmlentities($adresse['adresse']) . ', ' . htmlentities($adresse['codePostal']) . ', ' . htmlentities($adresse['ville']) . ', ' . htmlentities($adresse['pays']); ?>
                        <?php if (!empty($adresse['numeroBatiment'])) echo " (Bâtiment: " . htmlentities($adresse['numeroBatiment']) . ")"; ?>
                        <?php if (!empty($adresse['numeroAppartement'])) echo " (Appartement: " . htmlentities($adresse['numeroAppartement']) . ")"; ?>
                    </label>
                </div>
            <?php endforeach; ?>
            <a href="ajouterAdresse.php" class="btn btn-primary">Ajouter une Adresse</a>
        </div>

        <!-- Section pour la sélection des modes de paiement -->
        <div class="section">
            <h2>Vos Modes de Paiement</h2>
            <div class="card-container">
                <!-- Cartes Bancaires -->
                <?php if ($cartes): ?>
                    <?php foreach ($cartes as $carte): ?>
                        <label class="card-virtual">
                            <input type="radio" name="modePaiement" value="CB-<?php echo htmlentities($carte['numeroCarte']); ?>" required>
                            <div class="card-number">
                                **** **** **** <?php echo substr(htmlentities($carte['numeroCarte']), -4); ?>
                            </div>
                            <div class="card-expiration">
                                Exp : <?php echo substr(htmlentities($carte['dateExpiration']), 0, 2) . '/' . substr(htmlentities($carte['dateExpiration']), 2); ?>
                            </div>
                            <div class="card-holder">
                                <?php echo htmlentities($carte['titulaire']); ?>
                            </div>
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Carte pour ajouter une nouvelle carte -->
                <a href="ajouterCarte.php" class="card-add">+</a>
            </div>

            <!-- Option PayPal -->
            <div class="paypal-option">
                <label for="paypal" class="paypal-card">
                    <input type="radio" name="modePaiement" value="PayPal" id="paypal" required>
                    <img src="https://www.paypal.com/favicon.ico" alt="Logo PayPal">
                    <span>PayPal</span>
                </label>
            </div>
        </div>

        <!-- Section pour la sélection des remises de fidélité -->
        <div class="section">
            <h2>Utiliser vos points de fidélité</h2>
            <p>Vous avez <?php echo $pointF; ?> points de fidélité.</p>
            <div class="form-group">
                <label for="pointsFidelite">Sélectionnez une remise :</label>
                <select name="pointsFidelite" id="pointsFidelite" class="form-control">
                    <option value="0">Ne pas utiliser de points</option>
                    <?php if ($pointF >= 20): ?>
                        <option value="20">20 points = -5%</option>
                    <?php endif; ?>
                    <?php if ($pointF >= 50): ?>
                        <option value="50">50 points = -15%</option>
                    <?php endif; ?>
                    <?php if ($pointF >= 100): ?>
                        <option value="100">100 points = -35%</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <!-- Section pour afficher le total de la commande -->
        <div class="section">
            <h2>Total de la commande</h2>
            <p id="totalCommande" data-total="<?php echo $total; ?>">
                <span id="totalInitial"><?php echo $total; ?> €</span>
                <span id="totalReduit" class="text-danger" style="display: none;"></span>
            </p>
        </div>

        <!-- Bouton soumettre -->
        <button type="submit" class="btn btn-success">Payer</button>
    </form>
</main>

<script>
document.getElementById('pointsFidelite').addEventListener('change', function() {
    const totalInitial = parseFloat(document.getElementById('totalCommande').getAttribute('data-total'));
    const reduction = parseInt(this.value);
    let totalReduit = totalInitial;

    if (reduction === 20) {
        totalReduit = totalInitial * 0.95;
    } else if (reduction === 50) {
        totalReduit = totalInitial * 0.85;
    } else if (reduction === 100) {
        totalReduit = totalInitial * 0.65;
    }

    if (reduction > 0) {
        document.getElementById('totalInitial').style.textDecoration = 'line-through';
        document.getElementById('totalReduit').style.display = 'inline';
        document.getElementById('totalReduit').textContent = totalReduit.toFixed(2) + ' €';
    } else {
        document.getElementById('totalInitial').style.textDecoration = 'none';
        document.getElementById('totalReduit').style.display = 'none';
    }
});
</script>

<?php require_once './includes/footer.php'; ?>
