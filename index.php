<?php
// Inclusion de la configuration de la base de données
include 'Backend/config/db_connection.php';
session_start();

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="description"
        content="Mondial Automobile - Le concessionnaire automobile pour acheter ou vendre votre voiture d'occasion. Découvrez notre sélection de véhicules de qualité.">
    <title>Mondial Automobile | Concessionnaire Auto</title>

    <link rel="stylesheet" href="Frontend/css/style_index.css">
    <link rel="stylesheet" href="Frontend/css/style.css">
    <link rel="stylesheet" href="Frontend/css/style_alert.css">
    <script src="Frontend/js/alert.js" defer></script>

    <!-- Importation de la police Poppins depuis Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap"
        rel="stylesheet">

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const logoutLinks = document.querySelectorAll(".logout-link");
            logoutLinks.forEach(link => {
                link.addEventListener("click", (e) => {
                    e.preventDefault();
                    // Utilisation de l'alerte moderne
                    showAlert("Êtes-vous sûr de vouloir vous déconnecter ?", () => {
                        fetch('Backend/logout_handler.php', { method: 'POST' })
                            .then(() => window.location.reload());
                    });
                });
            });
        });
    </script>
</head>

<body>
    <!-- En-tête avec logo, menu et bannière principale -->
    <div class="header">
        <div class="container">
            <div class="navbar">
                <div class="logo">
                    <img src="Frontend/assets/images/logo.png" width="100px" alt="Logo Mondial Automobile">
                </div>
                <nav>
                    <ul id="MenuItems">
                        <li class="active"><a href="index.php">Accueil</a></li>
                        <li><a href="Frontend/vente.php">Ventes</a></li>
                        <li><a href="Frontend/reprise.php">Reprise</a></li>
                        <li class="dropdown">
                            <a href="Frontend/service.php">Service</a>
                            <ul class="dropdown-menu ">
                                <li><a href="Frontend/service-entretien.php">Carte grise / immatriculation</a></li>
                                <li><a href="Frontend/service-financement.php">Achat/Revente</a></li>
                                <li><a href="Frontend/service-financement.php">Nettoyage</a></li>
                                <li><a href="Frontend/service-garantie.php">Contrôle technique</a></li>
                            </ul>
                        </li>
                        <li><a href="Frontend/contact.php">Contact</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <li><a href="Frontend/admin.php">Administrateur</a></li>
                            <?php endif; ?>
                            <li><a href="javascript:void(0)" class="logout-link">Déconnexion</a></li>
                        <?php else: ?>
                            <li><a href="Frontend/connexion.php">Connexion</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <a href="Frontend/cart.php"><img src="Frontend/assets/images/cart.png" width="30px" height="30px" alt="Panier"></a>
            </div>

            <!-- Bannière d’accueil -->
            <section class="row">
                <div class="col-2">
                    <h1><span class="Mondial">Mondial</span> <span class="Automobile">Automobile</span></h1>
                    <a href="#about-section" class="btn">Découvrir →</a> <!-- Lien vers la section "À propos" -->
                </div>
            </section>
        </div>
    </div>

    <!-- Logo de contact en bas à gauche -->
    <div class="contact-logo">
        <a href="Frontend/contact.php"> <!-- Lien vers la page "contact.php" -->
            <img src="Frontend/assets/images/imagecontact2.png" alt="Logo Contact" />
        </a>
    </div>


    <!-- Section combinée À propos et Histoire -->
    <main class="about-history">
        <!-- Section À propos de l'entreprise -->
        <section id="about-section" class="about-section"> <!-- Ajout d'un ID pour la section "À propos" -->
            <div class="container">
                <h2>À propos de Mondial Automobile</h2>
                <p>
                    Mondial Automobile est votre partenaire de confiance pour l'achat, la vente ou la reprise de
                    véhicules d'occasion. Forts de plusieurs années d'expérience, nous vous accompagnons avec
                    professionnalisme et transparence pour trouver la voiture qui vous convient. Notre objectif : vous
                    offrir un service de qualité, à des prix compétitifs.
                </p>
            </div>
        </section>

        <!-- Section Histoire de Mondial Automobile -->
        <section class="history-section">
            <div class="container">
                <div class="history-content">
                    <div class="history-image">
                        <img src="Frontend/assets/images/ImageAbout.jpg" alt="Histoire de Mondial Automobile">
                    </div>
                    <div class="history-text">
                        <h2>L'Histoire de <br> Mondial Automobile</h2>
                        <p>
                            Mondial Automobile a été fondée il y a plus de 1 ans avec une mission simple : offrir aux
                            clients des véhicules d'occasion de qualité à des prix compétitifs. Notre équipe s'efforce
                            de vous fournir un service transparent, honnête et personnalisé pour chaque achat, vente ou
                            reprise. Nous croyons en une expérience d'achat sans stress, avec des véhicules
                            soigneusement sélectionnés et testés. Au fil des années, Mondial Automobile est devenu un
                            nom de confiance dans le domaine automobile à L'isle-Jourdain, offrant une large gamme de
                            voitures d'occasion
                            de toutes marques.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Elfsight Google Reviews | Untitled Google Reviews -->
        <script src="https://static.elfsight.com/platform/platform.js" async></script>
        <div class="elfsight-app-188e5a7e-e5cb-4411-a0f2-8c2c0858d7de" data-elfsight-app-lazy></div>
    </main>

    <script>
        // ...existing code...
    </script>
</body>

</html>