<?php
// Inclusion de la configuration de la base de données
include '../Backend/config/db_connection.php';
session_start();

// Vérification si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /MondialAutomobile/Frontend/connexion.php");
    exit();
}

// Suppression d'un utilisateur
if (isset($_GET['delete_user'])) {
    $user_id = intval($_GET['delete_user']);
    $delete_query = "DELETE FROM utilisateurs WHERE id = $user_id";
    $conn->query($delete_query);
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

// Récupération des membres pour affichage
$query = "SELECT id, username, email, role, date_creation FROM utilisateurs";
$result = $conn->query($query);
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
                            <ul class="dropdown-menu ">
                                <li><a href="/MondialAutomobile/Frontend/service-entretien.php">Carte grise / immatriculation</a></li>
                                <li><a href="/MondialAutomobile/Frontend/service-financement.php">Achat/Revente</a></li>
                                <li><a href="/MondialAutomobile/Frontend/service-financement.php">Nettoyage</a></li>
                                <li><a href="/MondialAutomobile/Frontend/service-garantie.php">Contrôle technique</a></li>
                            </ul>
                        </li>
                        <li><a href="/MondialAutomobile/Frontend/contact.php">Contact</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <li class="active"><a href="/MondialAutomobile/Frontend/admin.php">Administrateur</a></li>
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
                                <a href="?edit_user=<?php echo $row['id']; ?>" class="btn-edit">Modifier</a>
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
    </main>
</body>

</html>
