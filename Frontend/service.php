<?php
// Inclusion de la configuration de la base de données
include '../Backend/config/db_connection.php';
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services | Mondial Automobile</title>
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_service.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_alert.css">
    <script src="/MondialAutomobile/Frontend/js/alert.js" defer></script>
    <script src="/MondialAutomobile/Frontend/js/transition.js" defer></script>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Header Section -->
    <header class="header">
        <div class="container">
            <div class="navbar">
                <div class="logo">
                    <img src="assets/images/logo.png" width="100px" alt="Logo Mondial Automobile">
                </div>
                <nav>
                    <ul id="MenuItems">
                        <li><a href="/MondialAutomobile/Frontend/index.php">Accueil</a></li>
                        <li><a href="/MondialAutomobile/Frontend/vente.php">Ventes</a></li>
                        <li><a href="/MondialAutomobile/Frontend/reprise.php">Reprise</a></li>
                        <li class="dropdown active">
                            <a href="/MondialAutomobile/Frontend/service.php">Service</a>
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
    </header>

    <!-- Main Section -->
    <main class="service-container">
        <div class="container">
            <!-- Service 1 (Image on Right) -->
            <section id="carte-grise" class="service-row reverse">
                <div class="service-text">
                    <h2>Carte grise / immatriculation</h2>
                    <p>Vendez votre véhicule rapidement en toute simplicité :</p>
                    <ul>
                        <li>Une estimation gratuite et sans engagement</li>
                        <li>Une vente rapide et paiement garanti</li>
                        <li>Meilleur prix garanti</li>
                        <li>Aucune contrainte administrative</li>
                    </ul>
                    <a href="/MondialAutomobile/Frontend/contact.php" class="btn-service">Prendre RDV</a>
                </div>
                <div class="service-image">
                    <img src="assets/images/cartegrise.png" alt="Carte grise / immatriculation">
                </div>
            </section>

            <!-- Service 2 (Image on Left) -->
            <section id="achat-revente" class="service-row">
                <div class="service-image">
                    <img src="assets/images/Achatrevente.png" alt="Achat/Revente">
                </div>
                <div class="service-text">
                    <h2>Achat/Revente</h2>
                    <p>Besoin d'un remplacement ou d'une réparation d'impact sur votre pare-brise ?</p>
                    <ul>
                        <li>Intervention rapide et efficace</li>
                        <li>Garantie sur les réparations</li>
                        <li>Prise en charge des démarches administratives</li>
                    </ul>
                    <a href="/MondialAutomobile/Frontend/vente.php" class="btn-service">Contactez-nous</a>
                </div>
            </section>

            <!-- Service 3 (Image on Right) -->
            <section id="nettoyage" class="service-row reverse">
                <div class="service-text">
                    <h2>Nettoyage</h2>
                    <p>Confiez-nous l'entretien et la réparation de votre véhicule :</p>
                    <ul>
                        <li>Révision complète</li>
                        <li>Changement de pneus</li>
                        <li>Diagnostic électronique</li>
                        <li>Réparations mécaniques</li>
                    </ul>
                    <a href="/MondialAutomobile/Frontend/contact.php" class="btn-service">Prendre RDV</a>
                </div>
                <div class="service-image">
                    <img src="assets/images/nettoyage.png" alt="Nettoyage">
                </div>
            </section>

            <!-- Service 4 (Image on Left) -->
            <section id="controle-technique" class="service-row">
                <div class="service-image">
                    <img src="assets/images/Controletechnique.png" alt="Contrôle technique">
                </div>
                <div class="service-text">
                    <h2>Contrôle technique</h2>
                    <p>Confiez-nous l'entretien et la réparation de votre véhicule :</p>
                    <ul>
                        <li>Révision complète</li>
                        <li>Changement de pneus</li>
                        <li>Diagnostic électronique</li>
                        <li>Réparations mécaniques</li>
                    </ul>
                    <a href="/MondialAutomobile/Frontend/contact.php" class="btn-service">Prendre RDV</a>
                </div>
            </section>
        </div>
    </main>
</body>

</html>