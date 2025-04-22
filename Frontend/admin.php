<?php
// Inclusion de la configuration de la base de données
include '../Backend/config/db_connection.php';
session_start();

// Vérification si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /MondialAutomobile/Frontend/connexion.php");
    exit();
}

// Function to reset the IDs of the utilisateurs table
function resetUserIds($conn) {
    $conn->query("SET @count = 0");
    $conn->query("UPDATE utilisateurs SET id = (@count := @count + 1) ORDER BY id");
    $conn->query("ALTER TABLE utilisateurs AUTO_INCREMENT = 1");
}

// Suppression d'un utilisateur
if (isset($_GET['delete_user'])) {
    if ($_SESSION['role'] !== 'admin') {
        header("HTTP/1.1 403 Forbidden");
        exit();
    }
    $user_id = intval($_GET['delete_user']);
    $stmt = $conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Reset IDs after deletion
    resetUserIds($conn);

    header("Location: admin.php");
    exit();
}

// Suppression du compte de l'administrateur avec vérification du mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete_account'])) {
    $admin_id = $_SESSION['user_id'];
    $password = $_POST['password'];

    // Vérification du mot de passe
    $query = "SELECT mot_de_passe FROM utilisateurs WHERE id = $admin_id";
    $result = $conn->query($query);
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $delete_query = "DELETE FROM utilisateurs WHERE id = $admin_id";
        $conn->query($delete_query);
        session_destroy(); // Déconnexion automatique
        header("Location: /MondialAutomobile/Frontend/connexion.php");
        exit();
    } else {
        $error = "Mot de passe incorrect.";
    }
}

// Suppression d'un message
if (isset($_GET['delete_message'])) {
    if ($_SESSION['role'] !== 'admin') {
        header("HTTP/1.1 403 Forbidden");
        exit();
    }
    $message_id = intval($_GET['delete_message']);
    $stmt = $conn->prepare("DELETE FROM messages_contact WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin.php");
    exit();
}

// Suppression d'une demande de reprise
if (isset($_GET['delete_reprise'])) {
    if ($_SESSION['role'] !== 'admin') {
        header("HTTP/1.1 403 Forbidden");
        exit();
    }
    $reprise_id = intval($_GET['delete_reprise']);
    $stmt = $conn->prepare("DELETE FROM reprises WHERE id = ?");
    $stmt->bind_param("i", $reprise_id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin.php");
    exit();
}

// Modification d'un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $user_id = intval($_POST['user_id']);
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $role = $conn->real_escape_string($_POST['role']);

    // Vérification du rôle
    if (!in_array($role, ['admin', 'attente'])) {
        $role = 'attente'; // Par défaut, rôle "attente"
    }

    $update_query = "UPDATE utilisateurs SET username = '$username', email = '$email', role = '$role' WHERE id = $user_id";
    $conn->query($update_query);
    header("Location: admin.php");
    exit();
}

// Gestion du compte de l'administrateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_account'])) {
    $admin_id = $_SESSION['user_id'];
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = !empty($_POST['password']) ? $_POST['password'] : null;
    $confirm_password = !empty($_POST['confirm_password']) ? $_POST['confirm_password'] : null;

    if ($password && $password === $confirm_password) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $update_query = "UPDATE utilisateurs SET username = '$username', email = '$email', mot_de_passe = '$hashed_password' WHERE id = $admin_id";
        $success_message = "Le mot de passe a été modifié avec succès.";
    } elseif ($password && $password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        $update_query = "UPDATE utilisateurs SET username = '$username', email = '$email' WHERE id = $admin_id";
        $success_message = "Les informations ont été mises à jour avec succès.";
    }

    if (!isset($error)) {
        $conn->query($update_query);
        header("Location: admin.php?success=1");
        exit();
    }
}

// Récupération des membres pour affichage (exclure l'administrateur connecté)
$admin_id = $_SESSION['user_id'];
$query = "SELECT id, username, email, role, date_creation FROM utilisateurs WHERE id != $admin_id";
$result = $conn->query($query);

// Récupération des informations de l'administrateur
$account_query = "SELECT username, email, role, date_creation FROM utilisateurs WHERE id = $admin_id";
$account_result = $conn->query($account_query);
$account_info = $account_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrateur | Mondial Automobile</title>
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_admin.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_alert.css">

    <!-- Importation de la police Poppins depuis Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="/MondialAutomobile/Frontend/js/admin_effects.js" defer></script>
    <script src="/MondialAutomobile/Frontend/js/alert.js" defer></script>
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
                        <li><a href="/MondialAutomobile/Frontend/reprise.php">Reprise</a></li>
                        <li class="dropdown">
                            <a href="/MondialAutomobile/Frontend/service.php">Service</a>
                        </li>
                        <li><a href="/MondialAutomobile/Frontend/contact.php">Contact</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <li class="active"><a href="/MondialAutomobile/Frontend/admin.php">Compte</a></li>
                            <?php endif; ?>
                            <li><a href="javascript:void(0)" class="logout-link">Déconnexion</a></li>
                        <?php else: ?>
                            <li><a href="/MondialAutomobile/Frontend/connexion.php">Connexion</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <a href="cart.php"><img src="assets/images/cart.png" width="30px" height="30px" alt="Panier"></a>
            </div>

    <main class="admin-container">
        <section class="admin-section">
            <h2>Mon Compte</h2>
            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <p id="success-message" style="color: green; text-align: center;">Le mot de passe a été modifié avec succès.</p>
                <script>
                    setTimeout(() => {
                        const successMessage = document.getElementById('success-message');
                        if (successMessage) {
                            successMessage.style.display = 'none';
                        }
                    }, 3000); // 3 secondes
                </script>
            <?php endif; ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nom d'utilisateur</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $account_info['username']; ?></td>
                        <td><?php echo $account_info['email']; ?></td>
                        <td><?php echo ucfirst($account_info['role']); ?></td>
                        <td><?php echo $account_info['date_creation']; ?></td>
                        <td>
                            <a href="?edit_account=true" class="btn-edit">Modifier Mon Compte</a>
                            <a href="?delete_account=true" class="btn-delete">Supprimer</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <?php if (isset($_GET['edit_account'])): ?>
        <section class="admin-section2">
            <h2>Modifier Mon Compte</h2>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="input-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" value="<?php echo $account_info['username']; ?>" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $account_info['email']; ?>" required>
                </div>
                <div class="input-group">
                    <label for="password">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                    <input type="password" id="password" name="password">
                </div>
                <div class="input-group">
                    <label for="confirm_password">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password">
                </div>
                <button type="submit" name="update_account" class="btn-submit">Enregistrer les modifications</button>
            </form>
        </section>
        <?php endif; ?>

        <section class="admin-section">
            <h2>Gestion des Membres</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom d'utilisateur</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo ucfirst($row['role']); ?></td>
                            <td><?php echo $row['date_creation']; ?></td>
                            <td>
                                <a href="?edit_user=<?php echo $row['id']; ?>" class="btn-edit">Modifier Membre</a>
                                <a href="?delete_user=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <?php if (isset($_GET['edit_user'])): 
            $edit_user_id = intval($_GET['edit_user']);
            $edit_query = "SELECT * FROM utilisateurs WHERE id = $edit_user_id";
            $edit_result = $conn->query($edit_query);
            $edit_user = $edit_result->fetch_assoc();
        ?>
        <section class="admin-section2">
            <h2>Modifier un Utilisateur</h2>
            <form action="" method="POST">
                <input type="hidden" name="user_id" value="<?php echo $edit_user['id']; ?>">
                <div class="input-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" value="<?php echo $edit_user['username']; ?>" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $edit_user['email']; ?>" required>
                </div>
                <div class="input-group">
                    <label for="role">Rôle</label>
                    <select id="role" name="role" required>
                        <option value="admin" <?php echo $edit_user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="attente" <?php echo $edit_user['role'] === 'attente' ? 'selected' : ''; ?>>Attente</option>
                    </select>
                </div>
                <button type="submit" name="edit_user" class="btn-submit">Enregistrer les modifications</button>
            </form>
        </section>
        <?php endif; ?>

        <section class="admin-section">
            <h2>Messagerie</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Sujet</th>
                        <th>Message</th>
                        <th>Date d'envoi</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $messages_query = "SELECT id, nom, email, phone, sujet, message, date_envoi FROM messages_contact ORDER BY date_envoi DESC";
                    $messages_result = $conn->query($messages_query);
                    while ($message = $messages_result->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($message['nom']); ?></td>
                            <td><?php echo htmlspecialchars($message['email']); ?></td>
                            <td><?php echo htmlspecialchars($message['phone']); ?></td>
                            <td><?php echo htmlspecialchars($message['sujet']); ?></td>
                            <td><?php echo htmlspecialchars($message['message']); ?></td>
                            <td><?php echo $message['date_envoi']; ?></td>
                            <td>
                                <a href="?delete_message=<?php echo $message['id']; ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <section class="admin-section">
            <h2>Messagerie Reprise</h2>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Téléphone</th>
                            <th>Email</th>
                            <th>Marque</th>
                            <th>Modèle</th>
                            <th>Année</th>
                            <th>Kilométrage</th>
                            <th>Immatriculation</th>
                            <th>État</th>
                            <th>Historique</th>
                            <th>Description</th>
                            <th>Date de demande</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $reprises_query = "SELECT id, nom, telephone, email, marque, modele, annee, kilometrage, immatriculation, etat, historique, description, date_demande FROM reprises ORDER BY date_demande DESC";
                        $reprises_result = $conn->query($reprises_query);
                        while ($reprise = $reprises_result->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reprise['nom']); ?></td>
                                <td><?php echo htmlspecialchars($reprise['telephone']); ?></td>
                                <td><?php echo htmlspecialchars($reprise['email']); ?></td>
                                <td><?php echo htmlspecialchars($reprise['marque']); ?></td>
                                <td><?php echo htmlspecialchars($reprise['modele']); ?></td>
                                <td><?php echo htmlspecialchars($reprise['annee']); ?></td>
                                <td><?php echo htmlspecialchars($reprise['kilometrage']); ?> km</td>
                                <td><?php echo htmlspecialchars($reprise['immatriculation']); ?></td>
                                <td><?php echo htmlspecialchars($reprise['etat']); ?></td>
                                <td><?php echo htmlspecialchars($reprise['historique']); ?></td>
                                <td><?php echo htmlspecialchars($reprise['description']); ?></td>
                                <td><?php echo $reprise['date_demande']; ?></td>
                                <td>
                                    <a href="?delete_reprise=<?php echo $reprise['id']; ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette demande ?');">Supprimer</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>

</html>
