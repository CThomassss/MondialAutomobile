<?php
// Inclusion de la configuration de la base de données
include '../Backend/config/db_connection.php';
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');

    // Préparer la requête pour éviter les injections SQL
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['mot_de_passe'])) {
            // Connexion réussie, stockage des informations utilisateur dans la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: /MondialAutomobile/Frontend/index.php");
            exit();
        } else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        $error = "Aucun compte trouvé avec cet email.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mondial Automobile | Concessionnaire Auto</title>

    <!-- Feuilles de style -->
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_connexion.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_alert.css">

    <!-- Importation de la police Poppins depuis Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="/MondialAutomobile/Frontend/js/alert.js" defer></script>
    <script src="/MondialAutomobile/Frontend/js/transition.js" defer></script>
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
                        </li>
                        <li><a href="/MondialAutomobile/Frontend/contact.php">Contact</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <li><a href="/MondialAutomobile/Frontend/admin.php">Compte</a></li>
                            <?php endif; ?>
                            <li><a href="#" class="logout-link">Déconnexion</a></li>
                        <?php else: ?>
                            <li class="active"><a href="/MondialAutomobile/Frontend/connexion.php">Connexion</a></li>
                        <?php endif; ?>
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
                <?php if (isset($error)): ?>
                    <p style="color: red;"><?php echo $error; ?></p>
                <?php endif; ?>
                <form action="" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Entrez votre email" required>
                    </div>

                    <div class="input-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe"
                            required>
                    </div>

                    <button type="submit" class="btn-submit">Se connecter</button>
                </form>

                <!-- Lien vers l'inscription -->
                <p class="signup-link">Pas encore de compte ? <a href="/MondialAutomobile/Frontend/inscription.php">Inscrivez-vous</a></p>
            </div>
        </div>
    </main>
</body>

</html>