<?php
session_start();
require_once 'connect.inc.php'; // Connexion à la base de données

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['Suser'])) {
    header('Location: FormConnexion.php'); // Redirige vers la page de connexion si non connecté
    exit();
}

$idCompte = $_SESSION['Suser'];

if (isset($_POST['delete_card']) && $_POST['delete_card'] == 1 && isset($_POST['numeroCarte']) && isset($_POST['idCompte']) && $_POST['idCompte'] == $idCompte) {
    $numeroCarte = htmlentities($_POST['numeroCarte']);
    $idCompte = htmlentities($_POST['idCompte']);

    $stmtDelete = $conn->prepare("DELETE FROM Appartenir WHERE numeroCarte = :numeroCarte AND idCompte = :idCompte");
    $stmtDelete->execute([':numeroCarte' => $numeroCarte, ':idCompte' => $idCompte]);


    $reqSuppCarte = $conn->prepare("DELETE FROM CartesBancaire WHERE numeroCarte = :numeroCarte");
    $reqSuppCarte->execute([':numeroCarte' => $numeroCarte]);

    header('Location: modifMesinfos.php');
    exit();
}
// Requête pour récupérer les informations personnelles de l'utilisateur
$query = "
    SELECT c.prenom, c.nom, c.email, c.numeroTelephone
    FROM Comptes c
    WHERE c.idCompte = :idCompte
";

$stmt = $conn->prepare($query);
$stmt->execute(['idCompte' => $idCompte]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les adresses associées au compte
$stmtAdresses = $conn->prepare("
    SELECT a.idAdresse, a.pays, a.codePostal, a.adresse
    FROM Adresses a
    JOIN Resider r ON a.idAdresse = r.idAdresse
    WHERE r.idCompte = :idCompte
");
$stmtAdresses->execute([':idCompte' => $idCompte]);
$adresses = $stmtAdresses->fetchAll(PDO::FETCH_ASSOC);

function deleteAddress($conn, $idAdresse, $idCompte)
{
    try {
        // Vérifier le nombre d'adresses associées au compte
        $stmtCount = $conn->prepare("SELECT COUNT(*) FROM Resider WHERE idCompte = :idCompte");
        $stmtCount->execute([':idCompte' => $idCompte]);
        $count = $stmtCount->fetchColumn();

        if ($count <= 1) {
            $_SESSION['message'] = "Impossible de supprimer l'adresse. Vous devez avoir au moins une adresse.";
            header('Location: modifMesinfos.php');
            exit();
        }

        // Suppression de l'entrée dans Resider
        $stmtDeleteResider = $conn->prepare("DELETE FROM Resider WHERE idAdresse = :idAdresse and idCompte = :idCompte");
        $stmtDeleteResider->execute([':idAdresse' => $idAdresse, ':idCompte' => $idCompte]);

        // Suppression de l'adresse
        $stmtDeleteAdresse = $conn->prepare("DELETE FROM Adresses WHERE idAdresse = :idAdresse");
        $stmtDeleteAdresse->execute([':idAdresse' => $idAdresse]);

        $_SESSION['message'] = "L'adresse a été supprimée avec succès.";
        header('Location: modifMesinfos.php');
        exit();
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_address']) && isset($_POST['idAdresse'])) {
        $idAdresse = $_POST['idAdresse'];
        deleteAddress($conn, $idAdresse, $idCompte);
    } elseif (isset($_POST['update_info'])) {
        $prenom = htmlentities($_POST['prenom']);
        $nom = htmlentities($_POST['nom']);
        $email = htmlentities($_POST['email']);
        $numeroTelephone = htmlentities($_POST['numeroTelephone']);

        try {
            // Mise à jour des informations personnelles
            $stmtUpdate = $conn->prepare("
                UPDATE Comptes
                SET prenom = :prenom, nom = :nom, email = :email, numeroTelephone = :numeroTelephone
                WHERE idCompte = :idCompte
            ");
            $stmtUpdate->execute([
                ':prenom' => $prenom,
                ':nom' => $nom,
                ':email' => $email,
                ':numeroTelephone' => $numeroTelephone,
                ':idCompte' => $idCompte
            ]);

            $_SESSION['message'] = "Vos informations ont été mises à jour avec succès.";
            header('Location: modifMesinfos.php');
            exit();
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }
    } elseif (isset($_POST['add_address'])) {
        // Le bouton "Ajouter une adresse" a été cliqué, on affiche le formulaire d'ajout d'adresse
    } elseif (isset($_POST['confirm_new_address'])) {
        // Ajout d'une nouvelle adresse
        $pays = htmlentities($_POST['pays']);
        $codePostal = htmlentities($_POST['codePostal']);
        $adresse = htmlentities($_POST['adresse']);
        $numeroBatiment = !empty($_POST['numeroBatiment']) ? htmlentities($_POST['numeroBatiment']) : null;
        $numeroAppartement = !empty($_POST['numeroAppartement']) ? htmlentities($_POST['numeroAppartement']) : null;

        try {
            // Insérer la nouvelle adresse dans la table Adresses
            $stmtInsertAdresse = $conn->prepare("
                INSERT INTO Adresses (pays, codePostal, adresse, numeroBatiment, numeroAppartement)
                VALUES (:pays, :codePostal, :adresse, :numeroBatiment, :numeroAppartement)
            ");
            $stmtInsertAdresse->execute([
                ':pays' => $pays,
                ':codePostal' => $codePostal,
                ':adresse' => $adresse,
                ':numeroBatiment' => $numeroBatiment,
                ':numeroAppartement' => $numeroAppartement
            ]);

            // Récupérer l'ID de l'adresse insérée
            $idAdresse = $conn->lastInsertId();

            // Associer l'adresse au compte
            $stmtAssocierAdresse = $conn->prepare("
                INSERT INTO Resider (idCompte, idAdresse)
                VALUES (:idCompte, :idAdresse)
            ");
            $stmtAssocierAdresse->execute([
                ':idCompte' => $idCompte,
                ':idAdresse' => $idAdresse
            ]);

            // Redirection avec message de succès
            $_SESSION['message'] = "Votre adresse a été ajoutée avec succès.";
            header('Location: modifMesinfos.php');
            exit();
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }
    } elseif (isset($_POST['update_address']) && isset($_POST['idAdresse'])) {
        // Mise à jour d'une adresse existante
        $idAdresse = htmlentities($_POST['idAdresse']);
        $pays = htmlentities($_POST['pays']);
        $codePostal = htmlentities($_POST['codePostal']);
        $adresse = htmlentities($_POST['adresse']);

        try {
            // Mise à jour de l'adresse dans la table Adresses
            $stmtUpdateAdresse = $conn->prepare("
                UPDATE Adresses
                SET pays = :pays, codePostal = :codePostal, adresse = :adresse
                WHERE idAdresse = :idAdresse
            ");
            $stmtUpdateAdresse->execute([
                ':pays' => $pays,
                ':codePostal' => $codePostal,
                ':adresse' => $adresse,
                ':idAdresse' => $idAdresse
            ]);

            // Redirection avec message de succès
            $_SESSION['message'] = "L'adresse a été mise à jour avec succès.";
            header('Location: modifMesinfos.php');
            exit();
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Mes Informations</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./css/cb.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .dashboard-content h2 {
            text-align: center;
            color: var(--black);
        }

        .form-temp {
            background-color: var(--white);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .form-temp .form-group {
            margin-bottom: 1.5rem;
        }

        .form-temp .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: var(--black);
        }

        .form-temp .form-group input[type="text"],
        .form-temp .form-group input[type="email"],
        .form-temp .form-group input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--soft-grey);
            border-radius: 5px;
            font-size: 1rem;
            color: var(--black);
            background-color: var(--white);
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .form-temp .form-group input[type="submit"],
        .form-temp .btn {
            background-color: var(--monza);
            color: var(--white);
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .form-temp .form-group input[type="submit"]:hover,
        .form-temp .btn:hover {
            background-color: var(--light-red);
        }
    </style>
</head>

<body>
    <?php require_once './includes/header.php'; ?>

    <div class="dashboard-container">
        <aside class="sidebar">
            <nav>
                <ul>
                    <li><a href="monCompte.php">Accueil</a></li>
                    <li><a href="modifMesinfos.php">Modifier mes informations</a></li>
                    <li><a href="historiqueCommandes.php">Historique des commandes</a></li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <br>
            <h2>Mes informations</h2>
            <form action="modifMesinfos.php" method="POST" id="infoForm">
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>"
                        required>
                </div>
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"
                        required>
                </div>
                <div class="form-group">
                    <label for="numeroTelephone">Téléphone</label>
                    <input type="text" id="numeroTelephone" name="numeroTelephone"
                        value="<?= htmlspecialchars($user['numeroTelephone']) ?>" required>
                </div>
                <button type="submit" name="update_info" class="btn btn-primary" id="updateButton" disabled>Mettre à
                    jour</button>
            </form>

            <br><br>

            <?php
            $reqCarte = $conn->prepare("SELECT * FROM CartesBancaire c, Appartenir a WHERE a.numeroCarte=c.numeroCarte AND a.idCompte = :idCompte");
            $reqCarte->execute([':idCompte' => $idCompte]);
            ?>
            <div class="section">
                <h2>Vos Modes de Paiement</h2>
                <div class="card-container">
                    <!-- Cartes Bancaires -->
                    <?php if ($reqCarte->rowCount() > 0) {
                        $cartes = $reqCarte->fetchAll();

                        foreach ($cartes as $carte): ?>
                            <label class="card-virtual">
                                <form id="delete-card-form-<?php echo htmlentities($carte['numeroCarte']); ?>"
                                    action="modifMesinfos.php" method="POST" class="delete-card-form">
                                    <input type="hidden" name="delete_card" value="1">
                                    <input type="hidden" name="numeroCarte"
                                        value="<?php echo htmlentities($carte['numeroCarte']); ?>">
                                    <input type="hidden" name="idCompte" value="<?php echo $idCompte; ?>">
                                    <button type="submit" class="delete-button"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette carte ?')"><i
                                            class="fas fa-trash-alt"></i></button>
                                </form>

                                <div class="card-number">
                                    **** **** **** <?php echo substr(htmlentities($carte['numeroCarte']), -4); ?>
                                </div>
                                <div class="card-expiration">
                                    Exp :
                                    <?php echo substr(htmlentities($carte['dateExpiration']), 0, 2) . '/' . substr(htmlentities($carte['dateExpiration']), 2); ?>
                                </div>
                                <div class="card-holder">
                                    <?php echo htmlentities($carte['titulaire']); ?>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    <?php } ?>

                    <!-- Carte pour ajouter une nouvelle carte -->
                    <a href="ajouterCarte.php?modifcompte=ajoutercarte" class="card-add">+</a>
                </div>
            </div>

            <br><br>

            <div class="modif-adresse">
            <h2>Vos Adresses</h2>
            <form action="modifMesinfos.php" method="POST">
                <?php foreach ($adresses as $adresse): ?>
                    <div>
                        <label>
                            <input type="radio" name="idAdresse" value="<?= htmlentities($adresse['idAdresse']); ?>">
                            <?= htmlentities($adresse['adresse']) . ', ' . htmlentities($adresse['codePostal']) . ', ' . htmlentities($adresse['pays']); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
                <div class="btn-group">
                    <button type="submit" name="add_address" class="btn btn-success">Ajouter une adresse</button>
                    <button type="submit" name="select_address" class="btn btn-primary" <?= empty($adresses) ? 'disabled' : '' ?>>Modifier une adresse</button>
                    <button type="submit" name="delete_address" class="btn btn-danger" onclick="return confirmDelete();" <?= empty($adresses) ? 'disabled' : '' ?>>Supprimer une adresse</button>
                </div>
            </form>
            </div>

            <?php if (isset($_POST['add_address'])): ?>
                <h2>Ajouter une Nouvelle Adresse</h2>
                <form action="modifMesinfos.php" method="POST" class="form-temp" onsubmit="return confirmAddAddress()">
                    <div class="form-group">
                        <label for="pays" style="color: black;">Pays</label>
                        <input type="text" id="pays" name="pays" required>
                    </div>
                    <div class="form-group">
                        <label for="adresse" style="color: black;">Adresse</label>
                        <input type="text" id="adresse" name="adresse" required>
                    </div>
                    <div class="form-group">
                        <label for="codePostal" style="color: black;">Code Postal</label>
                        <input type="text" id="codePostal" name="codePostal" required pattern="\d{5}"
                            title="Veuillez entrer un code postal valide (5 chiffres)">
                    </div>
                    <div class="form-group">
                        <label for="numeroBatiment" style="color: black;">Numéro de Bâtiment (facultatif)</label>
                        <input type="text" id="numeroBatiment" name="numeroBatiment">
                    </div>
                    <div class="form-group">
                        <label for="numeroAppartement" style="color: black;">Numéro d'Appartement (facultatif)</label>
                        <input type="text" id="numeroAppartement" name="numeroAppartement">
                    </div>
                    <button type="submit" name="confirm_new_address" class="btn btn-primary">Ajouter l'Adresse</button>
                </form>
            <?php endif; ?>

            <?php if (isset($_POST['select_address']) && isset($_POST['idAdresse'])): ?>
                <?php
                $idAdresse = $_POST['idAdresse'];
                $stmtAdresse = $conn->prepare("SELECT * FROM Adresses WHERE idAdresse = :idAdresse");
                $stmtAdresse->execute([':idAdresse' => $idAdresse]);
                $adresse = $stmtAdresse->fetch(PDO::FETCH_ASSOC);
                ?>
                <h2>Modifier l'Adresse</h2>
                <form action="modifMesinfos.php" method="POST" class="form-temp" onsubmit="return confirmUpdate()">
                    <input type="hidden" name="idAdresse" value="<?= htmlspecialchars($adresse['idAdresse']) ?>">
                    <div class="form-group">
                        <label for="pays">Pays</label>
                        <input type="text" id="pays" name="pays" value="<?= htmlspecialchars($adresse['pays']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="adresse">Adresse</label>
                        <input type="text" id="adresse" name="adresse" value="<?= htmlspecialchars($adresse['adresse']) ?>"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="codePostal">Code Postal</label>
                        <input type="text" id="codePostal" name="codePostal"
                            value="<?= htmlspecialchars($adresse['codePostal']) ?>" required>
                    </div>
                    <button type="submit" name="update_address" class="btn btn-primary">Mettre à jour l'adresse</button>
                </form>
            <?php endif; ?>
        </main>
    </div>

    <script>
        // Fonction pour demander la confirmation avant de supprimer une adresse
        function confirmDelete() {
            return confirm("Êtes-vous sûr de vouloir supprimer cette adresse ?");
        }

        // Fonction pour confirmer la modification des informations
        function confirmUpdate() {
            return confirm("Êtes-vous sûr de vouloir modifier vos informations ?");
        }
        // Fonction pour confirmer l'ajout d'une adresse
        function confirmAddAddress() {
            return confirm("Êtes-vous sûr de vouloir ajouter cette adresse ?");
        }

        // Activer le bouton de mise à jour lorsque les informations sont modifiées
        const infoForm = document.getElementById('infoForm');
        const updateButton = document.getElementById('updateButton');
        const inputs = infoForm.querySelectorAll('input');

        inputs.forEach(input => {
            input.addEventListener('input', () => {
                updateButton.disabled = false;
            });
        });
    </script>

    <?php include './includes/footer.php'; ?>
</body>

</html>