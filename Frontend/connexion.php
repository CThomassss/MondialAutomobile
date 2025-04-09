<?php
// PHP code can be added here if needed in the future
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mondial Automobile | Concessionnaire Auto</title>

    <!-- Feuilles de style -->
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_connexion.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style.css">

    <!-- Importation de la police Poppins depuis Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body>
    <!-- En-tête avec logo, menu et bannière principale -->
    <header class="header">
        <div class="container">
            <div class="navbar">
                <div class="logo">
                    <img src="assets/images/logo.png" width="100" alt="Logo Mondial Automobile">
                </div>
                <nav>
                    <ul id="MenuItems">
                        <li class="active"><a href="/MondialAutomobile/Frontend/index.php">Accueil</a></li>
                        <li><a href="/MondialAutomobile/Frontend/vente.php">Ventes</a></li>
                        <li><a href="/MondialAutomobile/Frontend/reprise.php">Reprise</a></li>
                        <li class="dropdown">
                            <a href="/MondialAutomobile/Frontend/service.php">Service</a>
                            <ul class="dropdown-menu ">
                                <li><a href="/MondialAutomobile/Frontend/service-entretien.php">Carte grise / immatriculation</a></li>
                                <li><a href="/MondialAutomobile/Frontend/service-financement.php">Achat/Revente</a></li>
                                <li><a href="/MondialAutomobile/Frontend/service-financement.php">Nettoyage</a></li>
                                <li><a href="/MondialAutomobile/Frontend/service-garantie.php">Contrôle technique</a></li>
                            </ul>
                        </li>
                        <li><a href="/MondialAutomobile/Frontend/contact.php">Contact</a></li>
                        <li><a href="/MondialAutomobile/Frontend/connexion.php">Connexion</a></li>
                    </ul>
                </nav>
                <a href="cart.html">
                    <img src="assets/images/cart.png" width="30" height="30" alt="Panier">
                </a>
            </div>
        </div>
    </header>

    <!-- Conteneur principal de connexion -->
    <main class="login-container">
        <!-- Colonne gauche : illustration -->
        <div class="login-left">
            <div class="illustration">
                <img src="assets/images/Imagecoexio.webp" alt="Illustration Connexion">
            </div>
        </div>

        <!-- Colonne droite : formulaire -->
        <div class="login-right">
            <div class="form-container">
                <h1>Bienvenue sur <span>Mondial Automobile</span></h1>
                <p>Connectez-vous pour accéder à votre espace personnel.</p>

                <!-- Connexion avec Google -->
                <button class="google-login">
                    <img src="assets/images/google.svg" alt="Icône Google">
                    Connexion avec Google
                </button>

                <!-- Séparateur -->
                <div class="separator">
                    <span class="line"></span>
                    <span class="or">OU</span>
                    <span class="line"></span>
                </div>

                <!-- Formulaire de connexion -->
                <form action="#" method="POST">
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Entrez votre email" required>
                    </div>

                    <div class="input-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe"
                            required>
                    </div>

                    <div class="options">
                        <label>
                            <input type="checkbox" name="remember"> Se souvenir de moi
                        </label>
                        <a href="#">Mot de passe oublié ?</a>
                    </div>

                    <button type="submit" class="btn-submit">Se connecter</button>
                </form>

                <!-- Lien vers l'inscription -->
                <p class="signup-link">Pas encore de compte ? <a href="/Frontend/inscription.html">Inscrivez-vous</a>
                </p>
            </div>
        </div>
    </main>
</body>

</html>