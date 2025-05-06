<?php
// Inclusion de la configuration de la base de données
include '../Backend/config/db_connection.php';
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Limitation des tentatives de soumission du formulaire
if (!isset($_SESSION['contact_attempts'])) {
    $_SESSION['contact_attempts'] = 0;
}
if ($_SESSION['contact_attempts'] >= 5) {
    die("Trop de tentatives de soumission. Veuillez réessayer plus tard.");
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact | Mondial Automobile</title>
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/contact.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_alert.css">
    <script src="/MondialAutomobile/Frontend/js/alert.js" defer></script>
    <script src="/MondialAutomobile/Frontend/js/contact.js" defer></script>
    <script src="/MondialAutomobile/Frontend/js/transition.js" defer></script>
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
                    <img src="assets/images/logomondial.png" width="100px" alt="Logo Mondial Automobile">
                </div>
                <nav>
                    <ul id="MenuItems">
                        <li><a href="/MondialAutomobile/Frontend/index.php">Accueil</a></li>
                        <li><a href="/MondialAutomobile/Frontend/vente.php">Ventes</a></li>
                        <li><a href="/MondialAutomobile/Frontend/reprise.php">Reprise</a></li>
                        <li>
                            <a href="/MondialAutomobile/Frontend/service.php">Service</a>
                        </li>
                        <li class="dropdown active">
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
                <a href="cart.php"><img src="assets/images/cart.png" width="30px" height="30px" alt="Panier"></a>
            </div>
        </div>
    </div>
    <!-- Image à gauche -->
    <div class="contact-image">
                <img src="assets/images/bras.png" alt="Contact Image">
            </div>

    <!-- Section principale de contact -->
    <main class="contact-container">
        <div class="contact-layout">
            <!-- Section Contact -->
            <section class="contact-section">
                
                <h2>Contactez-nous</h2>
                <!-- Section pour appeler directement -->
                <div class="call-section">
                    <a href="tel:0623154908" class="btn-call">Appeler le 06 23 15 49 08</a>
                </div>
                <form action="/MondialAutomobile/Backend/contact_handler.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="input-group">
                        <label for="name">Nom</label>
                        <input type="text" id="name" name="name" placeholder="Entrez votre nom" required pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s]+" title="Le nom ne doit contenir que des lettres et des espaces.">
                    </div>
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Entrez votre email" required>
                    </div>
                    <div class="input-group">
                        <label for="phone">Téléphone</label>
                        <input type="tel" id="phone" name="phone" placeholder="Entrez votre numéro de téléphone" required pattern="\d{10}" title="Le numéro de téléphone doit contenir exactement 10 chiffres.">
                    </div>
                    <div class="input-group">
                        <label for="subject">Sujet</label>
                        <input type="text" id="subject" name="subject" placeholder="Entrez le sujet" required>
                    </div>
                    <div class="input-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="Entrez votre message" required></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Envoyer</button>
                </form>
            </section>

            <!-- Section Localisation -->
            <section class="map-section">
                <h2>Notre Localisation</h2>
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2886.517434123456!2d1.096434976852411!3d43.60403477110445!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12a94fb0f8c72919%3A0x490eb5021989d57c!2sMondial%20Automobile!5e0!3m2!1sfr!2sfr!4v1744624285765!5m2!1sfr!2sfr" 
                    width="100%" 
                    height="450" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </section>
        </div>
        <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/chatbot.css">
    </main>

    <!-- Chatbot Interface -->
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