<?php
// Include the database connection
include '../Backend/config/db_connection.php';

// Function to normalize text (remove accents, convert to lowercase, trim spaces)
function normalize_text($text) {
    $text = strtolower(trim($text));
    $text = str_replace(
        ['à', 'â', 'ä', 'é', 'è', 'ê', 'ë', 'î', 'ï', 'ô', 'ö', 'ù', 'û', 'ü', 'ç'],
        ['a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'o', 'o', 'u', 'u', 'u', 'c'],
        $text
    );
    return $text;
}

if (isset($_GET['question'])) {
    $user_question = normalize_text($_GET['question']); // Normalize user input

    $stmt = $conn->query("SELECT question, reponse FROM faq");
    $faqs = $stmt->fetch_all(MYSQLI_ASSOC);

    $best_match = null;
    $best_score = 0;

    foreach ($faqs as $faq) {
        $stored_question = normalize_text($faq['question']); // Normalize stored question
        similar_text($user_question, $stored_question, $percent);
        if ($percent > $best_score) {
            $best_score = $percent;
            $best_match = $faq;
        }
    }

    if ($best_score > 50) { // Set a similarity threshold of 50%
        echo $best_match['reponse'];
    } else {
        echo "Désolé, je n'ai pas compris la question. Pouvez-vous reformuler ?";
    }
}
?>
