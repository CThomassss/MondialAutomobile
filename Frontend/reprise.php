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
    <script src="/MondialAutomobile/Frontend/js/transition.js" defer></script>
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
        <form action="/MondialAutomobile/Backend/reprise_handler.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="form-row">
                <div class="input-group">
                    <label for="name">Nom</label>
                    <input type="text" id="name" name="name" placeholder="" required pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s]+" title="Le nom ne doit contenir que des lettres et des espaces.">
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="" required>
                </div>
                <div class="input-group">
                    <label for="phone">Téléphone</label>
                    <input type="tel" id="phone" name="phone" placeholder="" required pattern="\d{10}" title="Le numéro de téléphone doit contenir exactement 10 chiffres.">
                </div>
            </div>
            <div class="form-row">
                <div class="input-group">
                    <label for="marque">Marque</label>
                    <input type="text" id="marque" name="marque" placeholder="" required pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s]+" title="La marque ne doit contenir que des lettres et des espaces.">
                </div>
                <div class="input-group">
                    <label for="modele">Modèle</label>
                    <input type="text" id="modele" name="modele" placeholder="" required>
                </div>
                <div class="input-group">
                    <label for="annee">Année</label>
                    <input type="number" id="annee" name="annee" placeholder="" required min="1900" max="2099" title="L'année doit être comprise entre 1900 et 2099.">
                </div>
            </div>
            <div class="form-row">
                <div class="input-group">
                    <label for="kilometrage">Kilométrage</label>
                    <input type="number" id="kilometrage" name="kilometrage" placeholder="" required min="0" title="Le kilométrage doit être un nombre positif.">
                </div>
                <div class="input-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="2" placeholder=""></textarea>
                </div>
                <div class="input-group">
                    <label for="images">Images</label>
                    <input type="file" id="images" name="images[]" accept="image/*" multiple>
                </div>
            </div>
            <div class="form-row">
                <div class="input-group">
                    <label for="immatriculation">Immatriculation</label>
                    <input type="text" id="immatriculation" name="immatriculation" placeholder="" required pattern="[A-Z]{2}-\d{3}-[A-Z]{2}" title="Le format doit être AA-123-BB (exemple : AB-123-CD).">
                </div>
                <div class="input-group">
                    <label for="etat">État de la voiture</label>
                    <select id="etat" name="etat" required>
                        <option value="">Sélectionnez l'état</option>
                        <option value="neuf">Neuf</option>
                        <option value="occasion">Occasion</option>
                        <option value="accidente">Accidenté</option>
                    </select>
                </div>
                <div class="input-group">
                    <label for="historique">Historique d'achat</label>
                    <textarea id="historique" name="historique" rows="2" placeholder=""></textarea>
                </div>
            </div>
            <button type="submit" class="btn-submit">Envoyer</button>
        </form>
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
    <script>
        function validateForm() {
            const phone = document.getElementById('phone').value;
            if (!/^\d{10}$/.test(phone)) {
                alert('Le numéro de téléphone doit contenir exactement 10 chiffres.');
                return false;
            }
            return true;
        }
    </script>
</body>

</html>