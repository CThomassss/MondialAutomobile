<?php
include 'config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone']));
    $marque = htmlspecialchars(trim($_POST['marque']));
    $modele = htmlspecialchars(trim($_POST['modele']));
    $annee = intval($_POST['annee']);
    $kilometrage = intval($_POST['kilometrage']);
    $immatriculation = htmlspecialchars(trim($_POST['immatriculation']));
    $etat = htmlspecialchars(trim($_POST['etat']));
    $historique = htmlspecialchars(trim($_POST['historique']));

    if (!$email) {
        header("Location: /MondialAutomobile/Frontend/reprise.php?error=email_invalid");
        exit();
    }

    // Préparer la requête SQL
    $stmt = $conn->prepare("INSERT INTO reprises (nom, telephone, email, marque, modele, annee, kilometrage, statut, date_demande, immatriculation, etat, historique) VALUES (?, ?, ?, ?, ?, ?, ?, 'En attente', NOW(), ?, ?, ?)");

    // Vérifiez si la requête a été correctement préparée
    if (!$stmt) {
        error_log("Erreur SQL : " . $conn->error); // Enregistrer l'erreur dans les logs
        die("Erreur lors de la préparation de la requête : " . $conn->error); // Afficher une erreur pour le débogage
    }

    // Lier les paramètres
    $stmt->bind_param("sssssiisss", $name, $phone, $email, $marque, $modele, $annee, $kilometrage, $immatriculation, $etat, $historique);

    // Exécuter la requête
    if ($stmt->execute()) {
        header("Location: /MondialAutomobile/Frontend/reprise.php?success=1");
    } else {
        error_log("Erreur lors de l'exécution de la requête : " . $stmt->error); // Enregistrer l'erreur dans les logs
        header("Location: /MondialAutomobile/Frontend/reprise.php?error=1");
    }

    $stmt->close();
    exit();
}
?>
