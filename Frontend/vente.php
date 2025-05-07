<?php
// ----------------------
// INCLUSION ET SESSION
// ----------------------
include '../Backend/config/db_connection.php';
session_start();

// ----------------------
// PROTECTION XSS
// ----------------------
if (isset($_SESSION['username'])) {
    $_SESSION['username'] = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
}

// ----------------------
// RÉCUPÉRATION DES VÉHICULES
// ----------------------
$filter_query = "SELECT * FROM voitures";
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $filter_query .= " WHERE est_visible = 1"; // Only show visible vehicles for non-admin users
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
        $filter_query .= isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? " WHERE " : " AND ";
        $filter_query .= implode(" AND ", $filters);
    }
}

// ----------------------
// TRI DES VÉHICULES
// ----------------------
if (!empty($_GET['sort_by'])) {
    if ($_GET['sort_by'] === 'pertinence') {
        $filter_query .= " ORDER BY pertinence DESC, id DESC"; // Prioritize vehicles with "Pertinence" checked
    } elseif ($_GET['sort_by'] === 'price_asc') {
        $filter_query .= " ORDER BY prix ASC";
    } elseif ($_GET['sort_by'] === 'price_desc') {
        $filter_query .= " ORDER BY prix DESC";
    }
}

// ----------------------
// PAGINATION
// ----------------------
$annonces_par_page = 12; // Number of announcements per page
$page_actuelle = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1; // Current page
$offset = ($page_actuelle - 1) * $annonces_par_page; // Offset for SQL query

// Count total number of announcements
$total_annonces_query = "SELECT COUNT(*) AS total FROM voitures";
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $total_annonces_query .= " WHERE est_visible = 1";
}
$total_annonces_result = $conn->query($total_annonces_query);
$total_annonces = $total_annonces_result->fetch_assoc()['total'];
$total_pages = ceil($total_annonces / $annonces_par_page);

// Fetch announcements for the current page
$filter_query .= " LIMIT $annonces_par_page OFFSET $offset";
$result = $conn->query($filter_query);

// Gestion des erreurs SQL
if (!$result) {
    error_log("Erreur SQL : " . $conn->error);
    die("Une erreur est survenue. Veuillez réessayer plus tard.");
}

// ----------------------
// RÉCUPÉRATION DES MARQUES ET MODÈLES
// ----------------------
$marques_result = $conn->query("SELECT DISTINCT marque FROM voitures");
$marques = $marques_result->fetch_all(MYSQLI_ASSOC);

$modeles_result = $conn->query("SELECT DISTINCT modele FROM voitures");
$modeles = $modeles_result->fetch_all(MYSQLI_ASSOC);

// ----------------------
// AJOUT D'UNE ANNONCE (ADMIN)
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_vehicle']) && $_SESSION['role'] === 'admin') {
    $marque = $conn->real_escape_string(trim($_POST['marque']));
    $modele = $conn->real_escape_string(trim($_POST['modele']));
    $annee = intval($_POST['annee']);
    $prix = floatval($_POST['prix']);
    $kilometrage = intval($_POST['kilometrage']);
    $carburant = $conn->real_escape_string(trim($_POST['carburant']));
    $boite = $conn->real_escape_string(trim($_POST['boite']));
    $description = $conn->real_escape_string(trim($_POST['description']));

    // Validation des champs obligatoires
    if (empty($marque) || empty($modele) || empty($annee) || empty($prix) || empty($kilometrage) || empty($carburant) || empty($boite)) {
        die("Tous les champs obligatoires doivent être remplis.");
    }

    // Gestion de l'upload des images
    $image_paths = [];
    if (!empty($_FILES['images']['name'][0])) {
        $target_dir = "../Frontend/assets/uploads/";
        foreach ($_FILES['images']['name'] as $key => $image_name) {
            if ($_FILES['images']['error'][$key] !== UPLOAD_ERR_OK) {
                die("Erreur lors de l'upload du fichier : " . $_FILES['images']['name'][$key]);
            }

            $target_file = $target_dir . basename($image_name);
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Vérifiez que le fichier est une image valide
            $valid_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($file_type, $valid_extensions)) {
                if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target_file)) {
                    $image_paths[] = "assets/uploads/" . basename($image_name);
                } else {
                    die("Erreur lors du déplacement du fichier : " . $_FILES['images']['name'][$key]);
                }
            } else {
                die("Extension de fichier non valide pour : " . $_FILES['images']['name'][$key]);
            }
        }
    }

    // Si aucune image n'est téléchargée, définir une image par défaut
    if (empty($image_paths)) {
        $image_paths[] = "assets/images/car_placeholder.png";
    }

    // Convertir les chemins des images en JSON pour stockage
    $images_json = json_encode($image_paths);

    // Insertion dans la base de données
    $query = "INSERT INTO voitures (marque, modele, annee, prix, kilometrage, carburant, boite, description, images, est_visible) 
              VALUES ('$marque', '$modele', $annee, $prix, $kilometrage, '$carburant', '$boite', '$description', '$images_json', 1)";
    if (!$conn->query($query)) {
        die("Erreur lors de l'insertion dans la base de données : " . $conn->error);
    }

    // Redirection après succès
    header("Location: vente.php?success=1");
    exit();
}

// ----------------------
// MODIFICATION D'UNE ANNONCE (ADMIN)
// ----------------------
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
    $est_vendu = isset($_POST['est_vendu']) ? 1 : 0;
    $en_preparation = isset($_POST['en_preparation']) ? 1 : 0;
    $pertinence = isset($_POST['pertinence']) ? 1 : 0; // New field for "Pertinence"

    // Récupérer les images actuelles et l'image originale depuis la base de données
    $query = "SELECT images, image_originale FROM voitures WHERE id = $id";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $current_images = json_decode($row['images'], true);
    $image_originale = $row['image_originale'];

    if ($en_preparation) {
        // Sauvegarder l'image actuelle comme "image_originale" si elle n'est pas déjà définie
        if (empty($image_originale)) {
            $image_originale = $current_images[0];
        }
        // Remplacer l'image par "papa.jpg"
        $current_images = ["assets/images/papa.jpg"];
    } elseif (!$en_preparation && $current_images[0] === "assets/images/papa.jpg") {
        // Restaurer l'image originale si "Préparation" est décochée
        if (!empty($image_originale)) {
            $current_images = [$image_originale];
            $image_originale = null; // Réinitialiser l'image originale
        }
    } elseif (!empty($_FILES['image']['name'][0])) {
        $target_dir = "../Frontend/assets/uploads/";
        foreach ($_FILES['image']['name'] as $key => $image_name) {
            $target_file = $target_dir . basename($image_name);
            if (move_uploaded_file($_FILES['image']['tmp_name'][$key], $target_file)) {
                $current_images[] = "assets/uploads/" . basename($image_name);
            } else {
                die("Erreur lors du déplacement du fichier : " . $_FILES['image']['name'][$key]);
            }
        }
    }

    // Convertir les images en JSON et échapper correctement
    $images_json = $conn->real_escape_string(json_encode($current_images));
    $image_originale_sql = $image_originale ? "'" . $conn->real_escape_string($image_originale) . "'" : "NULL";

    // Mettre à jour la base de données
    $query = "UPDATE voitures SET 
              marque = '$marque', modele = '$modele', annee = $annee, prix = $prix, 
              kilometrage = $kilometrage, carburant = '$carburant', boite = '$boite', 
              description = '$description', images = '$images_json', image_originale = $image_originale_sql, 
              est_visible = $est_visible, est_vendu = $est_vendu, en_preparation = $en_preparation, pertinence = $pertinence 
              WHERE id = $id";

    if ($conn->query($query)) {
        header("Location: vente.php?success=2");
        exit();
    } else {
        die("Erreur lors de la mise à jour : " . $conn->error);
    }
}

// ----------------------
// SUPPRESSION D'UNE ANNONCE (ADMIN)
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_vehicle']) && $_SESSION['role'] === 'admin') {
    $id = intval($_POST['id']);
    $query = "DELETE FROM voitures WHERE id = $id";
    $conn->query($query);
    header("Location: vente.php?success=3");
    exit();
}

// ----------------------
// MARQUER UNE VOITURE COMME VENDUE
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_as_sold'])) {
    $id = intval($_POST['id']);
    $query = "UPDATE voitures SET est_vendu = 1 WHERE id = $id";
    if ($conn->query($query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}

// ----------------------
// RÉCUPÉRATION DYNAMIQUE DES MODÈLES
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marque'])) {
    $marque = $conn->real_escape_string($_POST['marque']);
    $query = "SELECT DISTINCT modele FROM voitures WHERE marque = '$marque'";
    $result = $conn->query($query);
    $modeles = [];
    while ($row = $result->fetch_assoc()) {
        $modeles[] = $row['modele'];
    }
    echo json_encode($modeles);
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <!-- ---------------------- -->
    <!-- MÉTADONNÉES ET CSS     -->
    <!-- ---------------------- -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vente | Mondial Automobile</title>
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_vente.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_alert.css">
    <script src="/MondialAutomobile/Frontend/js/alert.js" defer></script>
    <script src="/MondialAutomobile/Frontend/js/popup.js" defer></script>
    <script src="/MondialAutomobile/Frontend/js/transition.js" defer></script>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- ---------------------- -->
    <!-- SCRIPT POUR LES FILTRES -->
    <!-- ---------------------- -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const marqueSelect = document.querySelector('select[name="marque"]');
            const modeleSelect = document.querySelector('select[name="modele"]');

            marqueSelect.addEventListener('change', () => {
                const marque = marqueSelect.value;
                modeleSelect.innerHTML = '<option value="">Tous les modèles</option>'; // Reset modèles

                if (marque) {
                    fetch('vente.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `marque=${encodeURIComponent(marque)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(modele => {
                            const option = document.createElement('option');
                            option.value = modele;
                            option.textContent = modele;
                            modeleSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Erreur:', error));
                }
            });
        });
    </script>
</head>

<body>
    <!-- ---------------------- -->
    <!-- HEADER - EN-TÊTE        -->
    <!-- ---------------------- -->
    <header class="header">
        <div class="container">
            <div class="navbar">
                <div class="logo">
                <img src="assets/images/logomondial.png" width="100px" alt="Logo Mondial Automobile">
                </div>
                <nav>
                    <ul id="MenuItems">
                        <li><a href="/MondialAutomobile/index.php">Accueil</a></li>
                        <li class="active"><a href="/MondialAutomobile/Frontend/vente.php">Ventes</a></li>
                        <li><a href="/MondialAutomobile/Frontend/reprise.php">Reprise</a></li>
                        <li>
                            <a href="/MondialAutomobile/Frontend/service.php">Service</a>
                        </li>
                        <li class="dropdown">
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
    </header>

    <!-- ---------------------- -->
    <!-- SECTION DE FILTRAGE     -->
    <!-- ---------------------- -->
    <section class="filtre-section">
        <form method="GET" action="vente.php">
            <div class="filter-row">
                <select name="sort_by">
                    <option value="">Trier par</option>
                    <option value="pertinence" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] === 'pertinence' ? 'selected' : ''; ?>>Pertinence</option>
                    <option value="price_asc" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] === 'price_asc' ? 'selected' : ''; ?>>Prix croissant</option>
                    <option value="price_desc" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] === 'price_desc' ? 'selected' : ''; ?>>Prix décroissant</option>
                </select>
                <select name="marque">
                    <option value="">Toutes les marques</option>
                    <?php foreach ($marques as $marque): ?>
                        <option value="<?php echo htmlspecialchars($marque['marque'], ENT_QUOTES, 'UTF-8'); ?>" 
                            <?php echo isset($_GET['marque']) && $_GET['marque'] === $marque['marque'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($marque['marque'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="modele">
                    <option value="">Tous les modèles</option>
                    <?php foreach ($modeles as $modele): ?>
                        <option value="<?php echo htmlspecialchars($modele['modele'], ENT_QUOTES, 'UTF-8'); ?>" 
                            <?php echo isset($_GET['modele']) && $_GET['modele'] === $modele['modele'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($modele['modele'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="price-range">
                    <div>
                        <input type="number" name="prix_min" placeholder="Min €" value="<?php echo isset($_GET['prix_min']) ? htmlspecialchars($_GET['prix_min'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                        <input type="number" name="prix_max" placeholder="Max €" value="<?php echo isset($_GET['prix_max']) ? htmlspecialchars($_GET['prix_max'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                    </div>
                </div>
            </div>
            <div class="button-row">
                <button type="submit" class="btn-action">Filtrer</button>
                <a href="vente.php" class="btn-action btn-reset">Réinitialiser</a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <button type="button" class="btn-action" id="openPopup">Poster annonce</button>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <!-- ---------------------- -->
    <!-- FORMULAIRE ADMIN (AJOUT) -->
    <!-- ---------------------- -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <div id="popupForm" class="popup">
        <div class="popup-content">
            <span class="close-popup" id="closePopup">&times;</span>
            <h2>Poster une annonce</h2>
            <form method="POST" action="vente.php" enctype="multipart/form-data">
                <div class="form-row">
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
                </div>
                <div class="form-row">
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
                        <select id="carburant" name="carburant" required>
                            <option value="">Choisir le carburant</option>
                            <option value="Essence">Essence</option>
                            <option value="Diesel">Diesel</option>
                            <option value="Électrique">Électrique</option>
                            <option value="Hybride">Hybride</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="input-group">
                        <label for="boite">Boîte</label>
                        <select id="boite" name="boite" required>
                            <option value="">Choisir la boîte</option>
                            <option value="Manuelle">Manuelle</option>
                            <option value="Automatique">Automatique</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="2"></textarea>
                    </div>
                    <div class="input-group">
                        <label for="images">Images</label>
                        <input type="file" id="images" name="images[]" accept="image/*" multiple>
                    </div>
                </div>
                <div class="form-row" style="margin-top: 20px; justify-content: center;">
                    <button type="submit" name="add_vehicle" class="btn-submit">Poster</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- ---------------------- -->
    <!-- VITRINE DES VÉHICULES   -->
    <!-- ---------------------- -->
    <section class="vitrine-venda">
        <div class="annonces-container">
            <?php 
            $compteur = 0; // Counter to track announcements per row
            while ($vehicle = $result->fetch_assoc()): 
                if ($compteur % 4 === 0): // Start a new row every 4 announcements
            ?>
                <div class="row">
            <?php endif; ?>
                    <div class="carte">
                        <?php 
                        $images = json_decode($vehicle['images'], true);
                        $first_image = !empty($images) ? $images[0] : 'assets/images/car_placeholder.png';
                        ?>
                        <div class="carte-image-wrapper">
                            <img src="<?php echo htmlspecialchars($first_image, ENT_QUOTES, 'UTF-8'); ?>" alt="Image voiture">
                            <?php if ($vehicle['est_vendu'] == 1): ?>
                                <div class="vendu-overlay">
                                    <img src="assets/images/vendu.png" alt="Vendu">
                                </div>
                            <?php endif; ?>
                        </div>
                        <h2><?php echo htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele'], ENT_QUOTES, 'UTF-8'); ?></h2>
                        <p>Année : <?php echo htmlspecialchars($vehicle['annee'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p>Kilométrage : <?php echo htmlspecialchars($vehicle['kilometrage'], ENT_QUOTES, 'UTF-8'); ?> km</p>
                        <p class="prix"><?php echo htmlspecialchars(number_format($vehicle['prix'], 2, ',', ' '), ENT_QUOTES, 'UTF-8'); ?> €</p>
                        <a href="detail.php?id=<?php echo urlencode($vehicle['id']); ?>" class="btn-submit">Plus de détails</a>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <button class="btn-submit" onclick="openEditPopup(<?php echo htmlspecialchars(json_encode($vehicle), ENT_QUOTES, 'UTF-8'); ?>)">Modifier</button>
                            <form method="POST" action="vente.php" style="display:inline;" onsubmit="return confirmDelete();">
                                <input type="hidden" name="id" value="<?php echo $vehicle['id']; ?>">
                                <button type="submit" name="delete_vehicle" class="btn-submit" style="background-color: #af2e34;">Supprimer</button>
                            </form>
                            <p>Visible : <?php echo isset($vehicle['est_visible']) && $vehicle['est_visible'] ? 'Oui' : 'Non'; ?></p>
                        <?php endif; ?>
                    </div>
            <?php 
                $compteur++;
                if ($compteur % 4 === 0): // Close the row after 4 announcements
            ?>
                </div>
            <?php endif; ?>
            <?php endwhile; ?>
            <?php if ($compteur % 4 !== 0): // Close the last row if it's not complete ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ---------------------- -->
    <!-- PAGINATION              -->
    <!-- ---------------------- -->
    <div class="pagination">
        <?php if ($page_actuelle > 1): ?>
            <a href="?page=<?php echo $page_actuelle - 1; ?>" class="pagination-link">Précédent</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="pagination-link <?php echo $i === $page_actuelle ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
        <?php if ($page_actuelle < $total_pages): ?>
            <a href="?page=<?php echo $page_actuelle + 1; ?>" class="pagination-link">Suivant</a>
        <?php endif; ?>
    </div>

    <!-- ---------------------- -->
    <!-- FORMULAIRE ADMIN (MODIF) -->
    <!-- ---------------------- -->
    <div id="editPopupForm" class="popup">
        <div class="popup-content">
            <span class="close-popup" id="closeEditPopup">&times;</span>
            <h2>Modifier une annonce</h2>
            <form method="POST" action="vente.php" enctype="multipart/form-data">
                <input type="hidden" id="edit_id" name="id">
                <input type="hidden" id="current_image" name="current_image">
                <div class="form-row">
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
                </div>
                <div class="form-row">
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
                        <select id="edit_carburant" name="carburant" required>
                            <option value="">Choisir le carburant</option>
                            <option value="Essence">Essence</option>
                            <option value="Diesel">Diesel</option>
                            <option value="Électrique">Électrique</option>
                            <option value="Hybride">Hybride</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="input-group">
                        <label for="edit_boite">Boîte</label>
                        <select id="edit_boite" name="boite" required>
                            <option value="">Choisir la boîte</option>
                            <option value="Manuelle">Manuelle</option>
                            <option value="Automatique">Automatique</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="edit_description">Description</label>
                        <textarea id="edit_description" name="description" rows="2"></textarea>
                    </div>
                    <div class="input-group">
                        <label for="edit_image">Image</label>
                        <input type="file" id="edit_image" name="image[]" accept="image/*" multiple>
                    </div>
                </div>
                <div class="form-row visibility-row">
                    <div class="visibility-group">
                        <label for="edit_est_visible">Visibilité</label>
                        <input type="checkbox" id="edit_est_visible" name="est_visible">
                    </div>
                    <div class="visibility-group">
                        <label for="edit_est_vendu">Vendu</label>
                        <input type="checkbox" id="edit_est_vendu" name="est_vendu">
                    </div>
                    <div class="visibility-group">
                        <label for="edit_en_preparation">Préparation</label>
                        <input type="checkbox" id="edit_en_preparation" name="en_preparation">
                    </div>
                    <div class="visibility-group">
                        <label for="edit_pertinence">Pertinence</label>
                        <input type="checkbox" id="edit_pertinence" name="pertinence">
                    </div>
                </div>
                <div class="form-row" style="margin-top: 20px; justify-content: center;">
                    <button type="submit" name="edit_vehicle" class="btn-submit">Modifier</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ---------------------- -->
    <!-- SCRIPTS POUR LES ACTIONS -->
    <!-- ---------------------- -->
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
            document.getElementById('edit_est_vendu').checked = vehicle.est_vendu == 1;
            document.getElementById('edit_en_preparation').checked = vehicle.en_preparation == 1;
            document.getElementById('edit_pertinence').checked = vehicle.pertinence == 1;
            document.getElementById('editPopupForm').style.display = 'block';
        }

        document.getElementById('closeEditPopup').onclick = function() {
            document.getElementById('editPopupForm').style.display = 'none';
        };

        function confirmDelete() {
            return confirm("Êtes-vous sûr de vouloir supprimer cette annonce ?");
        }

        // Protection contre les attaques XSS dans les liens de déconnexion
        document.querySelectorAll('.logout-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                    window.location.href = '/MondialAutomobile/Backend/logout_handler.php';
                }
            });
        });
    </script>

</body>

</html>