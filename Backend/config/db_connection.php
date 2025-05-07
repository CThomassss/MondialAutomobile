<?php
// ----------------------
// CONFIGURATION DE LA BASE DE DONNÉES
// ----------------------
$host = 'localhost'; // Adresse du serveur MySQL
$username = 'root'; // Nom d'utilisateur MySQL
$password = ''; // Mot de passe MySQL (vide par défaut sur XAMPP)
$database = 'mondialautomobile'; // Nom de la base de données

// ----------------------
// CONNEXION À LA BASE DE DONNÉES
// ----------------------
$conn = new mysqli($host, $username, $password, $database);

// Vérifiez si la connexion a échoué
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}
?>
