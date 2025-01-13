<?php
require_once "connect.inc.php";

// Fonction pour vérifier qu'il y a au moins un produit dans le panier
function hasProductsInCart($conn, $idCommande) {
    if (!$conn) {
        return false;
    }
    $stmt = $conn->prepare("
        SELECT COUNT(*) as productCount FROM Quantites WHERE idCommande = :idCommande
    ");
    $stmt->execute([':idCommande' => $idCommande]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['productCount'] > 0;
}

// Fonction pour obtenir l'historique de recherche
function getSearchHistory($userId) {
    $historyData = isset($_COOKIE['historique']) ? json_decode($_COOKIE['historique'], true) : [];
    return array_filter($historyData[$userId] ?? [], function($product) {
        return $product['count'] >= 1;
    });
}

// Fonction pour supprimer un élément de l'historique
function deleteSearchHistoryItem($userId, $productId) {
    $historyData = isset($_COOKIE['historique']) ? json_decode($_COOKIE['historique'], true) : [];
    if (isset($historyData[$userId][$productId])) {
        unset($historyData[$userId][$productId]);
        setcookie('historique', json_encode($historyData), time() + 3600, '/');
    }
}

// Vérifier si la connexion est définie
$idCompte = $_SESSION['Suser'] ?? null;
$idCommande = null;

if (isset($conn) && $idCompte) {
    // Vérifier si une commande avec `estPanier = 1` existe pour ce compte
    $stmtCommande = $conn->prepare("
        SELECT idCommande FROM Commandes WHERE idCompte = :idCompte AND estPanier = 1
    ");
    $stmtCommande->execute([':idCompte' => $idCompte]);
    $commande = $stmtCommande->fetch(PDO::FETCH_ASSOC);

    if ($commande) {
        $idCommande = $commande['idCommande'];
    }
}
?>

<link rel="icon" type="image/png" href="images/logo.png" sizes="64x32">
<header class="navbar" style="width: 100%;">
    <div class="logo">
        <a href='./accueil.php'><img src="images/logo.png" alt="Logo" class="logo-image"></a>
    </div>

    <form action="./produits.php" method="get" autocomplete="off">
        <div class="search-container">
            <img src="https://cdn-icons-png.flaticon.com/512/694/694985.png" 
                alt="Loupe" class="search-icon" id="search-icon" />
            <input type="text" class="search-input" id="search-input" name="recherche" placeholder="Rechercher un produit" />
            <div id="search-results" class="search-results"></div> <!-- Résultats ici -->
            <div id="search-history" class="search-history"></div> <!-- Historique ici -->
        </div>
    </form>

    <nav class="menu">
        <ul>
            <li><a href="produits.php">Produits</a></li>
            <li><a href="produits.php?en_promo=on">Promotions</a></li>
            <li><a href="assistance.php">Contactez-nous</a></li>
            <li><a href="about-us.php">A propos</a></li>
        </ul>
        <div class="cart">
            <div class="dropdown">
                <img src="images/WEB_account_user.png" alt="Compte utilisateur" class="user-account">
                <?php
                if (isset($_SESSION['Suser'])) {
                    echo "<div class='dropdown-menu'>
                            <a href='monCompte.php'>Mon Compte</a>";

                    if (isset($_SESSION['Sadmin']) && $_SESSION['Sadmin'] == true) {
                        echo "<a href='dashboard.php'>Dashboard</a>";
                    }
                    echo "<a href='deconnexion.php'>Déconnexion</a></div>";
                } else {
                    echo "<div class='dropdown-menu'>
                                <a href='FormConnexion.php'>Connexion</a>
                                <a href='inscription.php'>Inscription</a>
                              </div>";
                }
                ?>
            </div>
            <a href="<?php echo isset($_SESSION['Suser']) || isset($_SESSION['Sadmin']) ? 'panier.php' : 'FormConnexion.php'; ?>" class="cart-link">
                <img src="images/WEB_shopping_cart.png" alt="Panier" class="cart-image">
            </a>
        </div>
    </nav>
</header>

    <script>
        const searchInput = document.getElementById('search-input');
        const searchIcon = document.getElementById('search-icon');
        const searchResults = document.getElementById('search-results');
        const searchHistory = document.getElementById('search-history');

        // Gérer les interactions avec l'historique et les résultats de recherche
        searchInput.addEventListener('focus', () => {
            if (searchInput.value.trim() === '') {
                fetch('search.php?action=history')
                    .then(response => response.text())
                    .then(data => {
                        searchHistory.innerHTML = data;
                        searchHistory.style.display = 'block';
                    })
                    .catch(error => console.error('Erreur lors de la récupération de l\'historique :', error));
            }
        });

        // Gérer le clic sur l'icône de recherche
        searchIcon.addEventListener('click', () => {
            if (searchInput.value.trim() !== '') {
                searchInput.closest('form').submit(); // Soumet le formulaire si la recherche n'est pas vide
            } else {
                alert('Veuillez saisir une recherche avant de continuer.');
            }
        });

        // Gérer la recherche en temps réel
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.trim();
            searchHistory.style.display = 'none'; // Cache l'historique des recherches

            if (query.length > 0) {
                fetch(`search.php?query=${encodeURIComponent(query)}`)
                    .then(response => response.text())
                    .then(data => {
                        searchResults.innerHTML = data;
                        searchResults.style.display = 'block'; // Affiche les résultats
                    })
                    .catch(error => {
                        console.error('Erreur lors de la recherche :', error);
                        searchResults.innerHTML = '<div class="no-results">Erreur de connexion.</div>';
                        searchResults.style.display = 'block';
                    });
            } else {
                searchResults.innerHTML = ''; // Vide les résultats
                searchResults.style.display = 'none'; // Cache la boîte
            }
        });

        // Afficher l'historique des recherches lors du focus sur l'input
        searchInput.addEventListener('focus', () => {
            if (searchInput.value.trim() === '') {
                fetch('search.php?action=history')
                    .then(response => response.text())
                    .then(data => {
                        searchHistory.innerHTML = data;
                        searchHistory.style.display = 'block';
                    })
                    .catch(error => console.error('Erreur lors de la récupération de l\'historique :', error));
            }
        });

        // Cache les résultats si on clique en dehors
        document.addEventListener('click', (event) => {
            if (!event.target.closest('.search-container')) {
                searchResults.style.display = 'none';
                searchHistory.style.display = 'none';
            }
        });

        // Empêcher la propagation lors du clic dans les résultats
        searchResults.addEventListener('click', (event) => {
            event.stopPropagation();
        });

        // Supprimer un élément de l'historique
        document.addEventListener('click', (event) => {
            if (event.target.classList.contains('delete-history')) {
                const productId = event.target.closest('.search-history-item').dataset.productId;
                fetch(`delete_history.php?productId=${productId}`)
                    .then(() => {
                        event.target.closest('.search-history-item').remove();
                    })
                    .catch(error => console.error('Erreur lors de la suppression de l\'historique :', error));
            }
        });

        // Accessibilité : gestion du clavier pour naviguer dans les résultats
        searchInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && searchInput.value.trim() !== '') {
                searchInput.closest('form').submit();
            }
        });
    </script>
    

<style src="./style.css"></style>
