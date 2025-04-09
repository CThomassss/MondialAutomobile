Remplacez TokenDemo2025 par votre clé API réelle.
Assurez-vous que l'API est accessible depuis votre serveur.


<?php
// Inclusion de la configuration de la base de données
include '../Backend/config/db_connection.php';
session_start();

$vehicleInfo = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['immatriculation'])) {
    $immatriculation = htmlspecialchars($_POST['immatriculation']);
    $apiUrl = "https://api.apiplaqueimmatriculation.com/get-vehicule-info?immatriculation=$immatriculation&token=TokenDemo2025&pays=FR";

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST'
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    $vehicleInfo = json_decode($response, true);

    if (!$vehicleInfo || isset($vehicleInfo['error'])) {
        $error = "Impossible de récupérer les informations pour cette immatriculation.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche d'immatriculation | Mondial Automobile</title>
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style.css">
    <link rel="stylesheet" href="/MondialAutomobile/Frontend/css/style_alert.css">
    <script src="/MondialAutomobile/Frontend/js/alert.js" defer></script>
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
                                <li><a href="/MondialAutomobile/Frontend/admin.php">Administrateur</a></li>
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

    <main class="container">
        <h1>Recherche d'immatriculation</h1>
        <form method="POST" action="">
            <label for="immatriculation">Entrez une immatriculation :</label>
            <input type="text" id="immatriculation" name="immatriculation" placeholder="Ex: AA-123-BC" required>
            <button type="submit" class="btn-submit">Rechercher</button>
        </form>

        <?php if ($error): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php elseif ($vehicleInfo): ?>
            <h2>Informations du véhicule :</h2>
            <ul>
                <li><strong>Marque :</strong> <?php echo $vehicleInfo['marque'] ?? 'Non disponible'; ?></li>
                <li><strong>Modèle :</strong> <?php echo $vehicleInfo['modele'] ?? 'Non disponible'; ?></li>
                <li><strong>Année :</strong> <?php echo $vehicleInfo['annee'] ?? 'Non disponible'; ?></li>
                <li><strong>Carburant :</strong> <?php echo $vehicleInfo['carburant'] ?? 'Non disponible'; ?></li>
                <!-- Ajoutez d'autres champs si nécessaire -->
            </ul>
        <?php endif; ?>
    </main>
</body>

</html>