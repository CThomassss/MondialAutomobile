<?php
// Inclusion de la configuration de la base de données
include '../Backend/config/db_connection.php';
session_start();

// Récupération des détails de la voiture
if (!isset($_GET['id'])) {
    header("Location: vente.php");
    exit();
}

$id = intval($_GET['id']);
$query = "SELECT * FROM voitures WHERE id = $id";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    header("Location: vente.php");
    exit();
}

$vehicle = $result->fetch_assoc();
$images = json_decode($vehicle['images'], true);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails | Mondial Automobile</title>
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_detail.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
</head>

<body>
    <!-- En-tête avec logo et menu -->
    <div class="header">
        <div class="container">
            <div class="navbar">
                <div class="logo">
                    <img src="assets/images/logomondial.png" width="100px" alt="Logo Mondial Automobile">
                </div>
                <nav>
                    <ul id="MenuItems">
                        <li><a href="/MondialAutomobile/Frontend/index.php">Accueil</a></li>
                        <li class="active"><a href="/MondialAutomobile/Frontend/vente.php">Ventes</a></li>
                        <li><a href="/MondialAutomobile/Frontend/reprise.php">Reprise</a></li>
                        <li><a href="/MondialAutomobile/Frontend/service.php">Service</a></li>
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
            </div>
        </div>
    </div>

    <!-- Section principale des détails -->
    <main class="detail-container">
        <div class="detail-content">
            <div class="detail-left">
                <!-- Carousel des images -->
                <div class="image-carousel">
                    <?php foreach ($images as $image): ?>
                        <div>
                            <img src="<?php echo htmlspecialchars($image); ?>" alt="Image voiture">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="detail-right">
                <div class="detail-header">
                    <h1><?php echo htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']); ?></h1>
                    <p class="price"><?php echo htmlspecialchars(number_format($vehicle['prix'], 2, ',', ' ')); ?> €</p>
                </div>
                <div class="detail-info">
                    <p class="icon-year"><strong>Année :</strong> <?php echo htmlspecialchars($vehicle['annee']); ?></p>
                    <p class="icon-kilometrage"><strong>Kilométrage :</strong> <?php echo htmlspecialchars($vehicle['kilometrage']); ?> km</p>
                    <p class="icon-energie"><strong>Carburant :</strong> <?php echo htmlspecialchars($vehicle['carburant']); ?></p>
                    <p class="icon-boite"><strong>Boîte :</strong> <?php echo htmlspecialchars($vehicle['boite']); ?></p>
                    <p class="icon-description"><strong>Description :</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($vehicle['description'])); ?></p>
                </div>
            </div>
        </div>
    </main>

    <script>
        $(document).ready(function () {
            $('.image-carousel').slick({
                dots: true,
                infinite: true,
                speed: 500,
                slidesToShow: 1,
                adaptiveHeight: true,
                arrows: true // Enable navigation arrows
            });
        });
    </script>
</body>

</html>