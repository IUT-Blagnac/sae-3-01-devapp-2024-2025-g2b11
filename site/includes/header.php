<link rel="icon" type="image/png" href="images/logo.png" sizes="64x64">
<header class="navbar">
    <div class="logo">
        <a href='./accueil.php'><img src="images/logo.png" alt="Logo" class="logo-image"></a>
    </div>

    <form action="./produits.php" method="get" autocomplete="off">
    <div class="search-container">
        <img src="https://cdn-icons-png.flaticon.com/512/694/694985.png" 
            alt="Loupe" class="search-icon" id="search-icon" />
        <input type="text" class="search-input" id="search-input" name="recherche" placeholder="Rechercher un produit" />
        <div id="search-results" class="search-results"></div> <!-- Résultats ici -->
    </div>
</form>




    <nav class="menu">
        <ul>
            <li><a href="produits.php">Produits</a></li>
            <li><a href="#">Nouveautés</a></li>
            <li><a href="#">Promotions</a></li>
            <li><a href="#">Assistance</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
    </nav>

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
        <a href="panier.php">
            <img src="images/WEB_shopping_cart.png" alt="Panier" class="cart-image">
        </a>
    </div>

    <script>
    const searchInput = document.getElementById('search-input');
    const searchIcon = document.getElementById('search-icon');
    const searchResults = document.getElementById('search-results');

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

    // Cache les résultats si on clique en dehors
    document.addEventListener('click', (event) => {
        if (!event.target.closest('.search-container')) {
            searchResults.style.display = 'none';
        }
    });

    // Empêcher la propagation lors du clic dans les résultats
    searchResults.addEventListener('click', (event) => {
        event.stopPropagation();
    });

    // Accessibilité : gestion du clavier pour naviguer dans les résultats
    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && searchInput.value.trim() !== '') {
            searchInput.closest('form').submit();
        }
    });

    </script>



    <style src="./style.css"></style>
</header>


