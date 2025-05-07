<?php
// ----------------------
// INCLUSION ET CONFIGURATION
// ----------------------
include './Backend/config/db_connection.php';
header('Content-Type: application/json');

// ----------------------
// FONCTIONS UTILITAIRES
// ----------------------
function sendResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit();
}

// ----------------------
// ROUTAGE DES REQUÊTES
// ----------------------
$requestMethod = $_SERVER['REQUEST_METHOD'];
$endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : null;

switch ($endpoint) {
    case 'vehicles': // Gestion des véhicules
        if ($requestMethod === 'GET') {
            getVehicles();
        } elseif ($requestMethod === 'POST') {
            createVehicle();
        } else {
            sendResponse(['error' => 'Méthode non autorisée'], 405);
        }
        break;

    case 'faq': // Gestion des FAQ pour le chatbot
        if ($requestMethod === 'GET') {
            getFaq();
        } else {
            sendResponse(['error' => 'Méthode non autorisée'], 405);
        }
        break;

    case 'chatbot': // Gestion des requêtes du chatbot
        if ($requestMethod === 'GET') {
            handleChatbot();
        } else {
            sendResponse(['error' => 'Méthode non autorisée'], 405);
        }
        break;

    default:
        sendResponse(['error' => 'Endpoint non trouvé'], 404);
}

// ----------------------
// HANDLERS DES ENDPOINTS
// ----------------------
function getVehicles() {
    global $conn;

    $query = "SELECT id, marque, modele, prix, annee, kilometrage FROM voitures WHERE est_visible = 1";
    $result = $conn->query($query);

    if ($result) {
        $vehicles = $result->fetch_all(MYSQLI_ASSOC);
        sendResponse($vehicles);
    } else {
        sendResponse(['error' => 'Erreur lors de la récupération des véhicules'], 500);
    }
}

function createVehicle() {
    global $conn;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['marque'], $data['modele'], $data['prix'], $data['annee'], $data['kilometrage'])) {
        sendResponse(['error' => 'Données manquantes'], 400);
    }

    $stmt = $conn->prepare("INSERT INTO voitures (marque, modele, prix, annee, kilometrage, est_visible) VALUES (?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("ssdii", $data['marque'], $data['modele'], $data['prix'], $data['annee'], $data['kilometrage']);

    if ($stmt->execute()) {
        $vehicleId = $stmt->insert_id; // Récupérer l'ID du véhicule ajouté
        sendResponse(['success' => 'Véhicule ajouté avec succès', 'vehicle_id' => $vehicleId]);
    } else {
        sendResponse(['error' => 'Erreur lors de l\'ajout du véhicule'], 500);
    }
}

function getFaq() {
    global $conn;

    $query = "SELECT question, reponse FROM faq";
    $result = $conn->query($query);

    if ($result) {
        $faq = $result->fetch_all(MYSQLI_ASSOC);
        sendResponse($faq);
    } else {
        sendResponse(['error' => 'Erreur lors de la récupération des FAQ'], 500);
    }
}

function handleChatbot() {
    global $conn;

    $question = isset($_GET['question']) ? trim($_GET['question']) : '';
    if (empty($question)) {
        sendResponse(['error' => 'Aucune question fournie'], 400);
    }

    $stmt = $conn->query("SELECT question, reponse FROM faq");
    $faqs = $stmt->fetch_all(MYSQLI_ASSOC);

    $bestMatch = null;
    $bestScore = 0;

    foreach ($faqs as $faq) {
        similar_text(strtolower($question), strtolower($faq['question']), $percent);
        if ($percent > $bestScore) {
            $bestScore = $percent;
            $bestMatch = $faq;
        }
    }

    if ($bestScore >= 70) { // Seuil de similarité
        sendResponse(['response' => $bestMatch['reponse']]);
    } else {
        sendResponse(['response' => 'Désolé, je n\'ai pas compris votre question. Pouvez-vous reformuler ?']);
    }
}
?>
