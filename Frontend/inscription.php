<?php
// Inclusion de la configuration de la base de données
include '../Backend/config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hashage du mot de passe

    // Vérification si l'email existe déjà
    $checkEmailQuery = "SELECT id FROM utilisateurs WHERE email = '$email'";
    $result = $conn->query($checkEmailQuery);

    if ($result->num_rows > 0) {
        $error = "Cet email est déjà utilisé.";
    } else {
        // Insertion dans la base de données
        $insertQuery = "INSERT INTO utilisateurs (username, email, mot_de_passe, role) VALUES ('$name', '$email', '$password', 'attente')";
        if ($conn->query($insertQuery)) {
            // Redirection après une inscription réussie
            header("Location: /MondialAutomobile/Frontend/connexion.php?success=1");
            exit();
        } else {
            $error = "Erreur lors de l'inscription : " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription | Mondial Automobile</title>
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_inscription.css">
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
                        <li><a href="/MondialAutomobile/Frontend/index.php">Accueil</a></li>
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
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <li class="active"><a href="/MondialAutomobile/Frontend/admin.php">Compte</a></li>
                        <?php endif; ?>
                        <li class="active"><a href="/MondialAutomobile/Frontend/connexion.php">Connexion</a></li>
                    </ul>
                </nav>
                <a href="cart.html">
                    <img src="assets/images/cart.png" width="30" height="30" alt="Panier">
                </a>
            </div>
        </div>
    </header>

    <main class="login-container">
        <div class="login-left">
            <div class="illustration">
                <img src="assets/images/Imagecoexio.webp" alt="Illustration Inscription">
            </div>
        </div>
        <div class="login-right">
            <div class="form-container">
                <h1>Créer un compte sur <span>Mondial Automobile</span></h1>
                <p>Remplissez le formulaire ci-dessous pour vous inscrire.</p>
                <?php if (isset($error)): ?>
                    <p style="color: red;"><?php echo $error; ?></p>
                <?php endif; ?>
                <form action="" method="POST">
                    <div class="input-group">
                        <label for="name">Nom</label>
                        <input type="text" id="name" name="name" placeholder="Entrez votre nom" required>
                    </div>
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Entrez votre email" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe"
                            required>
                    </div>
                    <button type="submit" class="btn-submit">S'inscrire</button>
                </form>
                <p class="signup-link">Déjà un compte ? <a href="/MondialAutomobile/Frontend/connexion.php">Connectez-vous</a></p>
            </div>
        </div>
    </main>
</body>

</html>