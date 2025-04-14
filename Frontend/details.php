<?php
include '../Backend/config/db_connection.php';
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
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
$images = json_decode($vehicle['images'], true) ?: [];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la voiture | Mondial Automobile</title>
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_details.css">
    <script src="/MondialAutomobile/Frontend/js/details.js" defer></script>
</head>

<body>
    <div class="details-container">
        <h1><?php echo htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']); ?></h1>
        <div class="carousel">
            <button class="carousel-arrow left-arrow" onclick="prevImage()">&#10094;</button>
            <div class="carousel-images">
                <?php foreach ($images as $index => $image): ?>
                    <img src="<?php echo htmlspecialchars($image); ?>" alt="Image voiture" class="carousel-image <?php echo $index === 0 ? 'active' : ''; ?>">
                <?php endforeach; ?>
            </div>
            <button class="carousel-arrow right-arrow" onclick="nextImage()">&#10095;</button>
        </div>
        <div class="details-info">
            <p><strong>Année :</strong> <?php echo htmlspecialchars($vehicle['annee']); ?></p>
            <p><strong>Kilométrage :</strong> <?php echo htmlspecialchars($vehicle['kilometrage']); ?> km</p>
            <p><strong>Prix :</strong> <?php echo htmlspecialchars(number_format($vehicle['prix'], 2, ',', ' ')); ?> €</p>
            <p><strong>Carburant :</strong> <?php echo htmlspecialchars($vehicle['carburant'] ?: 'Non spécifié'); ?></p>
            <p><strong>Boîte :</strong> <?php echo htmlspecialchars($vehicle['boite'] ?: 'Non spécifié'); ?></p>
            <p><strong>Description :</strong></p>
            <p><?php echo htmlspecialchars($vehicle['description'] ?: 'Aucune description disponible.'); ?></p>
        </div>
        <a href="vente.php" class="btn-submit">Retour</a>
    </div>
</body>

</html>
