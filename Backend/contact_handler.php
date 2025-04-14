<?php
include 'config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    if (!$email) {
        header("Location: /MondialAutomobile/Frontend/contact.php?error=email_invalid");
        exit();
    }

    // Préparer la requête pour éviter les injections SQL
    $stmt = $conn->prepare("INSERT INTO messages_contact (nom, email, phone, sujet, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);

    if ($stmt->execute()) {
        header("Location: /MondialAutomobile/Frontend/contact.php?success=1");
    } else {
        header("Location: /MondialAutomobile/Frontend/contact.php?error=1");
    }
    $stmt->close();
    exit();
}
?>
