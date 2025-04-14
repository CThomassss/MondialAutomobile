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
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la voiture | Mondial Automobile</title>
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_vente.css">
</head>

<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']); ?></h1>
        <?php 
        $images = json_decode($vehicle['images'], true);
        if (!empty($images)): ?>
            <div class="image-gallery">
                <?php foreach ($images as $image): ?>
                    <img src="<?php echo htmlspecialchars($image); ?>" alt="Image voiture" style="width: 100%; max-width: 600px; height: auto; border-radius: 8px; margin-bottom: 20px;">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <p><strong>Année :</strong> <?php echo htmlspecialchars($vehicle['annee']); ?></p>
        <p><strong>Kilométrage :</strong> <?php echo htmlspecialchars($vehicle['kilometrage']); ?> km</p>
        <p><strong>Prix :</strong> <?php echo htmlspecialchars(number_format($vehicle['prix'], 2, ',', ' ')); ?> €</p>
        <p><strong>Carburant :</strong> <?php echo htmlspecialchars($vehicle['carburant'] ?: 'Non spécifié'); ?></p>
        <p><strong>Boîte :</strong> <?php echo htmlspecialchars($vehicle['boite'] ?: 'Non spécifié'); ?></p>
        <p><strong>Description :</strong></p>
        <p><?php echo htmlspecialchars($vehicle['description'] ?: 'Aucune description disponible.'); ?></p>
        <a href="vente.php" class="btn-submit">Retour</a>
    </div>
</body>

</html>
