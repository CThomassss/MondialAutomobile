<?php
// ----------------------
// INCLUSION DES FICHIERS
// ----------------------
include '../Backend/config/db_connection.php';
session_start();

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <?php
    // ----------------------
    // MÉTADONNÉES
    // ----------------------
    ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="description"
        content="Mondial Automobile - Le concessionnaire automobile pour acheter ou vendre votre voiture d'occasion. Découvrez notre sélection de véhicules de qualité.">
    <title>Mondial Automobile | Concessionnaire Auto</title>

    <?php
    // ----------------------
    // LIENS CSS
    // ----------------------
    ?>
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_index.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_alert.css">

    <?php
    // ----------------------
    // SCRIPTS JAVASCRIPT
    // ----------------------
    ?>
    <script src="/MondialAutomobile/Frontend/js/alert.js" defer></script>
    <script src="/MondialAutomobile/Frontend/js/transition.js" defer></script>

    <?php
    // ----------------------
    // POLICE POPPINS
    // ----------------------
    ?>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap"
        rel="stylesheet">

    <?php
    // ----------------------
    // SCRIPT DÉCONNEXION
    // ----------------------
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const logoutLinks = document.querySelectorAll(".logout-link");
            logoutLinks.forEach(link => {
                link.addEventListener("click", (e) => {
                    e.preventDefault();
                    // Utilisation de l'alerte moderne
                    showAlert("Êtes-vous sûr de vouloir vous déconnecter ?", () => {
                        fetch('/MondialAutomobile/Backend/logout_handler.php', { method: 'POST' })
                            .then(() => window.location.reload());
                    });
                });
            });
        });
    </script>
</head>

<body>
    <?php
    // ----------------------
    // HEADER - EN-TÊTE
    // ----------------------
    ?>
    <div class="header">
        <div class="container">
            <div class="navbar">
                <!-- Logo -->
                <div class="logo">
                    <img src="assets/images/logomondial.png" width="100px" alt="Logo Mondial Automobile">
                </div>
                <!-- Menu de navigation -->
                <nav>
                    <ul id="MenuItems">
                        <li><a href="/MondialAutomobile/Frontend/index.php">Accueil</a></li>
                        <li><a href="/MondialAutomobile/Frontend/vente.php">Ventes</a></li>
                        <li><a href="/MondialAutomobile/Frontend/reprise.php">Reprise</a></li>
                        <li>
                            <a href="/MondialAutomobile/Frontend/service.php">Service</a>
                        </li>
                        <li class="dropdown">
                            <a href="/MondialAutomobile/Frontend/contact.php">Contact</a>
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
                                <ul class="dropdown-menu">
                                    <li><a href="/MondialAutomobile/Frontend/admin.php">Compte</a></li>
                                </ul>
                            <?php endif; ?>
                        </li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="javascript:void(0)" class="logout-link">Déconnexion</a></li>
                        <?php else: ?>
                            <li><a href="/MondialAutomobile/Frontend/connexion.php">Connexion</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <!-- Icône du panier -->
                <a href="cart.php"><img src="assets/images/cart.png" width="30px" height="30px" alt="Panier"></a>
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

    <?php
    // ----------------------
    // SECTION PRINCIPALE
    // ----------------------
    ?>
    <main class="about-history">
        <!-- Section À propos de l'entreprise -->
        <section id="about-section" class="about-section">
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
                        <img src="assets/images/ImageAbout.jpg" alt="Histoire de Mondial Automobile">
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

        <!-- Section des avis Google -->
        <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/chatbot.css">
        <script src="https://static.elfsight.com/platform/platform.js" async></script>
        <div class="elfsight-app-188e5a7e-e5cb-4411-a0f2-8c2c0858d7de" data-elfsight-app-lazy></div>
    </main>

    <?php
    // ----------------------
    // SCRIPTS D'ANIMATIONS
    // ----------------------
    ?>
    <script>
        // Animation de défilement horizontal pour l'image
        window.addEventListener('scroll', () => {
            const slideImage = document.querySelector('.history-image img');
            const scrollPosition = window.pageYOffset;
            slideImage.style.transform = `translateX(${scrollPosition * 0.05}px)`; // Déplacement horizontal
        });

        // Effet d'apparition pour l'image
        window.addEventListener('load', () => {
            const historyImage = document.querySelector('.history-image img');
            historyImage.style.opacity = '0'; // Initialement invisible
            historyImage.style.transform = 'scale(0.8)'; // Réduction initiale
            setTimeout(() => {
                historyImage.style.transition = 'opacity 1s ease, transform 1s ease'; // Transition fluide
                historyImage.style.opacity = '1'; // Apparition
                historyImage.style.transform = 'scale(1)'; // Taille normale
            }, 200);
        });

        // Barre animée entre les sections
        const aboutHistorySection = document.querySelector('.about-history');
        const animatedBar = document.createElement('div');
        animatedBar.style.width = '80%'; // Largeur de la barre
        animatedBar.style.height = '5px'; // Hauteur de la barre
        animatedBar.style.backgroundColor = '#000'; // Couleur noire
        animatedBar.style.margin = '20px auto'; // Centrée avec marges
        animatedBar.style.position = 'relative';
        animatedBar.style.overflow = 'hidden';
        aboutHistorySection.insertBefore(animatedBar, aboutHistorySection.children[1]); // Insertion dans le DOM

        const movingBar = document.createElement('div');
        movingBar.style.width = '20%'; // Largeur de la barre rouge
        movingBar.style.height = '100%'; // Hauteur identique à la barre noire
        movingBar.style.backgroundColor = '#ebd8d8'; // Couleur blanche
        movingBar.style.position = 'absolute';
        movingBar.style.left = '-20%'; // Position initiale hors de la barre noire
        movingBar.style.animation = 'moveBar 5s linear infinite'; // Animation continue avec une durée plus lente
        animatedBar.appendChild(movingBar);

        const style = document.createElement('style');
        style.textContent = `
            @keyframes moveBar {
                0% {
                    left: -20%; /* Départ hors de la barre noire */
                }
                100% {
                    left: 100%; /* Fin hors de la barre noire */
                }
            }
        `;
        document.head.appendChild(style); // Ajout des styles dans le <head>

        // Effet d'apparition pour une photo de personnage
        const aboutContainer = document.querySelector('.about-section .container');
        const personImage = document.createElement('img'); // Création de l'image du personnage
        personImage.src = 'assets/images/imagepersonne.webp'; // Chemin de l'image du personnage
        personImage.alt = 'Photo de personnage';
        personImage.style.width = '170px'; // Taille initiale de l'image
        personImage.style.position = 'absolute';
        personImage.style.right = '-200px'; // Position initiale hors de l'écran
        personImage.style.transition = 'right 1s ease, transform 0.3s ease'; // Transition fluide pour la position et l'agrandissement
        aboutContainer.style.position = 'relative'; // Positionnement relatif pour le conteneur
        aboutContainer.appendChild(personImage); // Ajout de l'image dans le DOM

        window.addEventListener('scroll', () => {
            const aboutRect = aboutContainer.getBoundingClientRect();
            if (aboutRect.top <= window.innerHeight && aboutRect.bottom >= 0) {
                personImage.style.right = '100px'; // Position finale visible
                personImage.style.transform = 'scale(1.2)'; // Agrandissement de l'image
            } else {
                personImage.style.right = '-200px'; // Réinitialisation hors de l'écran
                personImage.style.transform = 'scale(1)'; // Retour à la taille normale
            }
        });
    </script>

    <?php
    // ----------------------
    // CHATBOT
    // ----------------------
    ?>
    <div id="chatbot" class="chatbot-container">
        <div class="chatbot-header">
            <h3>Chatbot</h3>
            <button id="closeChatbot">&times;</button>
        </div>
        <div class="chatbot-messages" id="chatbotMessages">
            <div class="message bot">Bienvenue sur MondialAutomobile, que voulez-vous savoir sur notre activité ?</div>
        </div>
        <form id="chatbotForm">
            <input type="text" id="chatbotInput" placeholder="Écrivez un message..." required>
            <button type="submit">Envoyer</button>
        </form>
    </div>
    <button id="openChatbot" class="chatbot-toggle">💬</button>

    <script src="/MondialAutomobile/Frontend/js/chatbot.js" defer></script>
</body>

</html>