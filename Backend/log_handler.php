function logActivity($userId, $action, $details, $conn) {
    $stmt = $conn->prepare("INSERT INTO logs (user_id, action, details, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $userId, $action, $details);
    $stmt->execute();
    $stmt->close();
}
