<?php
// Inclusion de la configuration de la base de données
include '../Backend/config/db_connection.php';
session_start();

// Vérification si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /MondialAutomobile/Frontend/connexion.php");
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
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_admin.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style.css">
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
                                <a href="#" class="btn-edit">Modifier</a>
                                <a href="#" class="btn-delete">Supprimer</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>

</html>
