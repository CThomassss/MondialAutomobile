<?php
// Inclusion de la configuration de la base de données
include '../Backend/config/db_connection.php';
session_start();

// Récupération des véhicules disponibles
$filter_query = "SELECT * FROM voitures WHERE est_vendu = 0";
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $filters = [];
    if (!empty($_GET['marque'])) {
        $filters[] = "marque LIKE '%" . $conn->real_escape_string($_GET['marque']) . "%'";
    }
    if (!empty($_GET['modele'])) {
        $filters[] = "modele LIKE '%" . $conn->real_escape_string($_GET['modele']) . "%'";
    }
    if (!empty($_GET['prix_min'])) {
        $filters[] = "prix >= " . intval($_GET['prix_min']);
    }
    if (!empty($_GET['prix_max'])) {
        $filters[] = "prix <= " . intval($_GET['prix_max']);
    }
    if ($filters) {
        $filter_query .= " AND " . implode(" AND ", $filters);
    }
}
$result = $conn->query($filter_query);

// Ajout d'une annonce (réservé aux administrateurs)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_vehicle']) && $_SESSION['role'] === 'admin') {
    $marque = $conn->real_escape_string($_POST['marque']);
    $modele = $conn->real_escape_string($_POST['modele']);
    $annee = intval($_POST['annee']);
    $prix = floatval($_POST['prix']);
    $kilometrage = intval($_POST['kilometrage']);
    $carburant = $conn->real_escape_string($_POST['carburant']);
    $boite = $conn->real_escape_string($_POST['boite']);
    $description = $conn->real_escape_string($_POST['description']);

    // Gestion de l'upload de l'image
    $image_path = 'assets/images/car_placeholder.png'; // Valeur par défaut
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../Frontend/assets/uploads/";
        $target_file = $target_dir . basename($_FILES['image']['name']);
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Vérifiez que le fichier est une image valide
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_type, $valid_extensions)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = "assets/uploads/" . basename($_FILES['image']['name']);
            }
        }
    }

    $query = "INSERT INTO voitures (marque, modele, annee, prix, kilometrage, carburant, boite, description, image_path) 
              VALUES ('$marque', '$modele', $annee, $prix, $kilometrage, '$carburant', '$boite', '$description', '$image_path')";
    $conn->query($query);
    header("Location: vente.php?success=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vente | Mondial Automobile</title>
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_vente.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_alert.css">
    <script src="/MondialAutomobile/Frontend/js/alert.js" defer></script>
    <script src="/MondialAutomobile/Frontend/js/popup.js" defer></script>
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
                    <img src="assets/images/logo.png" width="100px" alt="Logo Mondial Automobile">
                </div>
                <nav>
                    <ul id="MenuItems">
                        <li><a href="/MondialAutomobile/Frontend/index.php">Accueil</a></li>
                        <li class="active"><a href="/MondialAutomobile/Frontend/vente.php">Ventes</a></li>
                        <li><a href="/MondialAutomobile/Frontend/reprise.php">Reprise</a></li>
                        <li class="dropdown">
                            <a href="/MondialAutomobile/Frontend/service.php">Service</a>
                            <ul class="dropdown-menu">
                                <li><a href="/MondialAutomobile/Frontend/service-entretien.php">Carte grise / immatriculation</a></li>
                                <li><a href="/MondialAutomobile/Frontend/service-financement.php">Achat/Revente</a></li>
                                <li><a href="/MondialAutomobile/Frontend/service-financement.php">Nettoyage</a></li>
                                <li><a href="/MondialAutomobile/Frontend/service-garantie.php">Contrôle technique</a></li>
                            </ul>
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

    <!-- Section de filtrage -->
    <section class="filtre-section">
        <form method="GET" action="vente.php">
            <input type="text" name="marque" placeholder="Marque">
            <input type="text" name="modele" placeholder="Modèle">
            <input type="number" name="prix_min" placeholder="Prix min">
            <input type="number" name="prix_max" placeholder="Prix max">
            <button type="submit" class="btn-submit">Filtrer</button>
        </form>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <button type="button" class="btn-submit" id="openPopup">Poster une annonce</button>
        <?php endif; ?>
    </section>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <div id="popupForm" class="popup">
        <div class="popup-content">
            <span class="close-popup" id="closePopup">&times;</span>
            <h2>Poster une annonce</h2>
            <form method="POST" action="vente.php" enctype="multipart/form-data">
                <div class="input-group">
                    <label for="marque">Marque</label>
                    <input type="text" id="marque" name="marque" required>
                </div>
                <div class="input-group">
                    <label for="modele">Modèle</label>
                    <input type="text" id="modele" name="modele" required>
                </div>
                <div class="input-group">
                    <label for="annee">Année</label>
                    <input type="number" id="annee" name="annee" required>
                </div>
                <div class="input-group">
                    <label for="prix">Prix</label>
                    <input type="number" id="prix" name="prix" required>
                </div>
                <div class="input-group">
                    <label for="kilometrage">Kilométrage</label>
                    <input type="number" id="kilometrage" name="kilometrage" required>
                </div>
                <div class="input-group">
                    <label for="carburant">Carburant</label>
                    <input type="text" id="carburant" name="carburant">
                </div>
                <div class="input-group">
                    <label for="boite">Boîte</label>
                    <input type="text" id="boite" name="boite">
                </div>
                <div class="input-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"></textarea>
                </div>
                <div class="input-group">
                    <label for="image">Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
                <button type="submit" name="add_vehicle" class="btn-submit">Poster</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Section vitrine -->
    <section class="vitrine-venda">
        <?php while ($vehicle = $result->fetch_assoc()): ?>
            <div class="carte">
                <img src="<?php echo htmlspecialchars($vehicle['image_path']); ?>" alt="Image voiture">
                <h2><?php echo htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']); ?></h2>
                <p>Année : <?php echo htmlspecialchars($vehicle['annee']); ?></p>
                <p>Kilométrage : <?php echo htmlspecialchars($vehicle['kilometrage']); ?> km</p>
                <p class="prix"><?php echo htmlspecialchars(number_format($vehicle['prix'], 2, ',', ' ')); ?> €</p>
                <p><?php echo htmlspecialchars($vehicle['description']); ?></p>
            </div>
        <?php endwhile; ?>
    </section>
</body>

</html>