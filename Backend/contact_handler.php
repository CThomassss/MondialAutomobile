<?php
include 'config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);

    // Insérer le message dans la table `messages_contact`
    $query = "INSERT INTO messages_contact (nom, email, phone, sujet, message) VALUES ('$name', '$email', '$phone', '$subject', '$message')";
    if ($conn->query($query)) {
        header("Location: /MondialAutomobile/Frontend/contact.php?success=1");
    } else {
        header("Location: /MondialAutomobile/Frontend/contact.php?error=1");
    }
    exit();
}
?>
