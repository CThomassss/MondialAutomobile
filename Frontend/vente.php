<?php
// Inclusion de la configuration de la base de données
include '../Backend/config/db_connection.php';
session_start();

// Récupération des véhicules disponibles
$filter_query = "SELECT * FROM voitures WHERE est_vendu = 0";
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $filter_query .= " AND est_visible = 1"; // Only show visible vehicles for non-admin users
}
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

// Récupérer les marques distinctes
$marques_result = $conn->query("SELECT DISTINCT marque FROM voitures WHERE est_vendu = 0");
$marques = $marques_result->fetch_all(MYSQLI_ASSOC);

// Récupérer les modèles distincts
$modeles_result = $conn->query("SELECT DISTINCT modele FROM voitures WHERE est_vendu = 0");
$modeles = $modeles_result->fetch_all(MYSQLI_ASSOC);

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

    // Gestion de l'upload des images
    $image_paths = [];
    if (!empty($_FILES['images']['name'][0])) {
        $target_dir = "../Frontend/assets/uploads/";
        foreach ($_FILES['images']['name'] as $key => $image_name) {
            $target_file = $target_dir . basename($image_name);
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Vérifiez que le fichier est une image valide
            $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($file_type, $valid_extensions)) {
                if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target_file)) {
                    $image_paths[] = "assets/uploads/" . basename($image_name);
                }
            }
        }
    }

    // Convertir les chemins des images en JSON pour stockage
    $images_json = json_encode($image_paths);

    $query = "INSERT INTO voitures (marque, modele, annee, prix, kilometrage, carburant, boite, description, images) 
              VALUES ('$marque', '$modele', $annee, $prix, $kilometrage, '$carburant', '$boite', '$description', '$images_json')";
    $conn->query($query);
    header("Location: vente.php?success=1");
    exit();
}

// Modification d'une annonce (réservé aux administrateurs)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_vehicle']) && $_SESSION['role'] === 'admin') {
    $id = intval($_POST['id']);
    $marque = $conn->real_escape_string($_POST['marque']);
    $modele = $conn->real_escape_string($_POST['modele']);
    $annee = intval($_POST['annee']);
    $prix = floatval($_POST['prix']);
    $kilometrage = intval($_POST['kilometrage']);
    $carburant = $conn->real_escape_string($_POST['carburant']);
    $boite = $conn->real_escape_string($_POST['boite']);
    $description = $conn->real_escape_string($_POST['description']);
    $est_visible = isset($_POST['est_visible']) ? 1 : 0;

    // Gestion de l'upload de l'image
    $image_path = $_POST['current_image'];
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

    $query = "UPDATE voitures SET 
              marque = '$marque', modele = '$modele', annee = $annee, prix = $prix, 
              kilometrage = $kilometrage, carburant = '$carburant', boite = '$boite', 
              description = '$description', images = '$image_path', est_visible = $est_visible 
              WHERE id = $id";
    $conn->query($query);
    header("Location: vente.php?success=2");
    exit();
}

// Suppression d'une annonce (réservé aux administrateurs)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_vehicle']) && $_SESSION['role'] === 'admin') {
    $id = intval($_POST['id']);
    $query = "DELETE FROM voitures WHERE id = $id";
    $conn->query($query);
    header("Location: vente.php?success=3");
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
            <select name="marque">
                <option value="">Toutes les marques</option>
                <?php foreach ($marques as $marque): ?>
                    <option value="<?php echo htmlspecialchars($marque['marque']); ?>" 
                        <?php echo isset($_GET['marque']) && $_GET['marque'] === $marque['marque'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($marque['marque']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="modele">
                <option value="">Tous les modèles</option>
                <?php foreach ($modeles as $modele): ?>
                    <option value="<?php echo htmlspecialchars($modele['modele']); ?>" 
                        <?php echo isset($_GET['modele']) && $_GET['modele'] === $modele['modele'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($modele['modele']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="prix_min" placeholder="Prix min" value="<?php echo isset($_GET['prix_min']) ? htmlspecialchars($_GET['prix_min']) : ''; ?>">
            <input type="number" name="prix_max" placeholder="Prix max" value="<?php echo isset($_GET['prix_max']) ? htmlspecialchars($_GET['prix_max']) : ''; ?>">
            <button type="submit" class="btn-submit">Filtrer</button>
            <a href="vente.php" class="btn-reset">Réinitialiser</a>
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
                    <label for="images">Images</label>
                    <input type="file" id="images" name="images[]" accept="image/*" multiple>
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
                <?php 
                // Décoder les chemins d'images stockés en JSON
                $images = json_decode($vehicle['images'], true);
                $first_image = !empty($images) ? $images[0] : 'assets/images/car_placeholder.png'; // Image par défaut si aucune image n'est disponible
                ?>
                <img src="<?php echo htmlspecialchars($first_image); ?>" alt="Image voiture">
                <h2><?php echo htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']); ?></h2>
                <p>Année : <?php echo htmlspecialchars($vehicle['annee']); ?></p>
                <p>Kilométrage : <?php echo htmlspecialchars($vehicle['kilometrage']); ?> km</p>
                <p class="prix"><?php echo htmlspecialchars(number_format($vehicle['prix'], 2, ',', ' ')); ?> €</p>
                <button class="btn-submit">Plus de détails</button>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <p>Visible : <?php echo isset($vehicle['est_visible']) && $vehicle['est_visible'] ? 'Oui' : 'Non'; ?></p>
                    <button class="btn-submit" onclick="openEditPopup(<?php echo htmlspecialchars(json_encode($vehicle)); ?>)">Modifier</button>
                    <form method="POST" action="vente.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $vehicle['id']; ?>">
                        <button type="submit" name="delete_vehicle" class="btn-submit" style="background-color: #af2e34;">Supprimer</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </section>

    <div id="editPopupForm" class="popup">
        <div class="popup-content">
            <span class="close-popup" id="closeEditPopup">&times;</span>
            <h2>Modifier une annonce</h2>
            <form method="POST" action="vente.php" enctype="multipart/form-data">
                <input type="hidden" id="edit_id" name="id">
                <input type="hidden" id="current_image" name="current_image">
                <div class="input-group">
                    <label for="edit_marque">Marque</label>
                    <input type="text" id="edit_marque" name="marque" required>
                </div>
                <div class="input-group">
                    <label for="edit_modele">Modèle</label>
                    <input type="text" id="edit_modele" name="modele" required>
                </div>
                <div class="input-group">
                    <label for="edit_annee">Année</label>
                    <input type="number" id="edit_annee" name="annee" required>
                </div>
                <div class="input-group">
                    <label for="edit_prix">Prix</label>
                    <input type="number" id="edit_prix" name="prix" required>
                </div>
                <div class="input-group">
                    <label for="edit_kilometrage">Kilométrage</label>
                    <input type="number" id="edit_kilometrage" name="kilometrage" required>
                </div>
                <div class="input-group">
                    <label for="edit_carburant">Carburant</label>
                    <input type="text" id="edit_carburant" name="carburant">
                </div>
                <div class="input-group">
                    <label for="edit_boite">Boîte</label>
                    <input type="text" id="edit_boite" name="boite">
                </div>
                <div class="input-group">
                    <label for="edit_description">Description</label>
                    <textarea id="edit_description" name="description" rows="4"></textarea>
                </div>
                <div class="input-group">
                    <label for="edit_image">Image</label>
                    <input type="file" id="edit_image" name="image" accept="image/*">
                </div>
                <div class="input-group">
                    <label for="edit_est_visible">Visible</label>
                    <input type="checkbox" id="edit_est_visible" name="est_visible">
                </div>
                <button type="submit" name="edit_vehicle" class="btn-submit">Modifier</button>
            </form>
        </div>
    </div>

    <script>
        function openEditPopup(vehicle) {
            document.getElementById('edit_id').value = vehicle.id;
            document.getElementById('edit_marque').value = vehicle.marque;
            document.getElementById('edit_modele').value = vehicle.modele;
            document.getElementById('edit_annee').value = vehicle.annee;
            document.getElementById('edit_prix').value = vehicle.prix;
            document.getElementById('edit_kilometrage').value = vehicle.kilometrage;
            document.getElementById('edit_carburant').value = vehicle.carburant;
            document.getElementById('edit_boite').value = vehicle.boite;
            document.getElementById('edit_description').value = vehicle.description;
            document.getElementById('current_image').value = vehicle.images;
            document.getElementById('edit_est_visible').checked = vehicle.est_visible == 1;
            document.getElementById('editPopupForm').style.display = 'block';
        }

        document.getElementById('closeEditPopup').onclick = function() {
            document.getElementById('editPopupForm').style.display = 'none';
        };
    </script>
</body>

</html>