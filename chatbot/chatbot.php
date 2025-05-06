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

// Function to calculate similarity score
function calculate_similarity($input, $stored) {
    similar_text($input, $stored, $percent);
    return $percent;
}

if (isset($_GET['question'])) {
    $user_question = normalize_text($_GET['question']); // Normalize user input

    // Fetch all questions and answers from the database
    $stmt = $conn->query("SELECT question, reponse FROM faq");
    $faqs = $stmt->fetch_all(MYSQLI_ASSOC);

    $best_match = null;
    $best_score = 0;

    foreach ($faqs as $faq) {
        $stored_question = normalize_text($faq['question']); // Normalize stored question
        $score = calculate_similarity($user_question, $stored_question);

        // Update the best match if the score is higher
        if ($score > $best_score) {
            $best_score = $score;
            $best_match = $faq;
        }
    }

    // Set a higher similarity threshold for better accuracy
    if ($best_score >= 70) { // 70% similarity threshold
        echo $best_match['reponse'];
    } elseif ($best_score >= 50) { // 50% similarity threshold
        echo "Je pense que vous voulez dire : \"" . $best_match['question'] . "\". Voici la réponse : " . $best_match['reponse'];
    } else {
        // Suggest reformulating the question if no good match is found
        echo "Désolé, je n'ai pas compris votre question. Pouvez-vous reformuler ou poser une autre question ?";
    }
}
?>
