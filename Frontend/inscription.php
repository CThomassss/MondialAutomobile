<?php
// Inclusion de la configuration de la base de données
include '../Backend/config/db_connection.php';

session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Limitation des tentatives d'inscription
if (!isset($_SESSION['signup_attempts'])) {
    $_SESSION['signup_attempts'] = 0;
}
if ($_SESSION['signup_attempts'] >= 5) {
    die("Trop de tentatives d'inscription. Veuillez réessayer plus tard.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide.";
    } elseif (strlen($password) < 8) {
        $error = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/\d/', $password)) {
        $error = "Le mot de passe doit contenir au moins une lettre majuscule, une lettre minuscule et un chiffre.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Vérifier si l'email existe déjà
        $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Cet email est déjà utilisé.";
        } else {
            // Insérer l'utilisateur
            $stmt = $conn->prepare("INSERT INTO utilisateurs (username, email, mot_de_passe, role) VALUES (?, ?, ?, 'attente')");
            $stmt->bind_param("sss", $name, $email, $hashed_password);
            if ($stmt->execute()) {
                $_SESSION['signup_attempts'] = 0; // Réinitialiser les tentatives après un succès
                header("Location: /MondialAutomobile/Frontend/connexion.php?success=1");
                exit();
            } else {
                $error = "Erreur lors de l'inscription.";
            }
        }
        $stmt->close();
    }

    $_SESSION['signup_attempts']++; // Incrémenter les tentatives en cas d'échec
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
    <script src="/MondialAutomobile/Frontend/js/transition.js" defer></script>
</head>

<body>

    <!-- En-tête avec logo, menu et bannière principale -->
<header class="header">
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
                        <li class="dropdown">
                            <a href="/MondialAutomobile/Frontend/contact.php">Contact</a>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <ul class="dropdown-menu">
                                    <?php if ($_SESSION['role'] === 'admin'): ?>
                                        <li><a href="/MondialAutomobile/Frontend/admin.php">Compte</a></li>
                                    <?php endif; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
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
                    <p style="color: red; text-align: center;"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <form action="" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
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