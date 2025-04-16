<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reprise | Mondial Automobile</title>
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_reprise.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_alert.css">
    <script src="/MondialAutomobile/Frontend/js/alert.js" defer></script>
    <!-- Importation de la police Poppins depuis Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body>
    <!-- En-tête avec logo, menu et bannière principale -->
    <div class="header">
        <div class="container">
            <div class="navbar">
                <div class="logo">
                    <img src="assets/images/logo.png" width="100px" alt="Logo Mondial Automobile">
                </div>
                <nav>
                    <ul id="MenuItems">
                        <li><a href="/MondialAutomobile/Frontend/index.php">Accueil</a></li>
                        <li><a href="/MondialAutomobile/Frontend/vente.php">Ventes</a></li>
                        <li class="active"><a href="/MondialAutomobile/Frontend/reprise.php">Reprise</a></li>
                        <li class="dropdown">
                            <a href="/MondialAutomobile/Frontend/service.php">Service</a>
                            <ul class="dropdown-menu">
                                <li><a href="/MondialAutomobile/Frontend/service-entretien.php">Carte grise / immatriculation</a></li>
                                <li><a href="/MondialAutomobile/Frontend/service-financement.php">Achat/Revente</a></li>
                                <li><a href="/MondialAutomobile/Frontend/service-financement.php">Nettoyage</a></li>
                                <li><a href="/MondialAutomobile/Frontend/service-garantie.php">Contrôle technique</a></li>
                            </ul>
                        </li>
                        <li><a href="/MondialAutomobile/Frontend/contact.php">Contact</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <li><a href="/MondialAutomobile/Frontend/admin.php">Compte</a></li>
                            <?php endif; ?>
                            <li><a href="javascript:void(0)" class="logout-link">Déconnexion</a></li>
                        <?php else: ?>
                            <li><a href="/MondialAutomobile/Frontend/connexion.php">Connexion</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <a href="cart.php"><img src="assets/images/cart.png" width="30px" height="30px" alt="Panier"></a>
            </div>
        </div>
    </div>

    <!-- Section principale de reprise -->
    <main class="reprise-container">
        <h2>Proposez votre véhicule à la reprise</h2>
        <form action="/MondialAutomobile/Backend/reprise_handler.php" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="input-group">
                    <label for="name">Nom</label>
                    <input type="text" id="name" name="name" placeholder="Entrez votre nom" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Entrez votre email" required>
                </div>
                <div class="input-group">
                    <label for="phone">Téléphone</label>
                    <input type="tel" id="phone" name="phone" placeholder="Entrez votre numéro de téléphone" required>
                </div>
            </div>
            <div class="form-row">
                <div class="input-group">
                    <label for="marque">Marque</label>
                    <input type="text" id="marque" name="marque" placeholder="Entrez la marque de votre véhicule" required>
                </div>
                <div class="input-group">
                    <label for="modele">Modèle</label>
                    <input type="text" id="modele" name="modele" placeholder="Entrez le modèle de votre véhicule" required>
                </div>
                <div class="input-group">
                    <label for="annee">Année</label>
                    <input type="number" id="annee" name="annee" placeholder="Entrez l'année de mise en circulation" required>
                </div>
            </div>
            <div class="form-row">
                <div class="input-group">
                    <label for="kilometrage">Kilométrage</label>
                    <input type="number" id="kilometrage" name="kilometrage" placeholder="Entrez le kilométrage" required>
                </div>
                <div class="input-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="2" placeholder="Ajoutez une description"></textarea>
                </div>
                <div class="input-group">
                    <label for="images">Images</label>
                    <input type="file" id="images" name="images[]" accept="image/*" multiple>
                </div>
            </div>
            <button type="submit" class="btn-submit">Envoyer</button>
        </form>
    </main>
</body>

</html>