<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set content type to JSON
header('Content-Type: application/json');

// Load database config
require_once 'config/database.php';

// Database connection
$mysqli = new mysqli($db_host, $db_user, $db_pass, 'webbycms_knowledge');

// Check connection
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $mysqli->connect_error]);
    exit;
}

// Get search keyword from query string
$keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';
$escaped_keywords = $mysqli->real_escape_string($keywords);

// Base SQL
$sql = "
    SELECT 
        q.id AS question_id,
        q.user_id,
        q.featured,
        q.views,
        q.created,
        q.modified,
        q.status,
        q_lang_question.content AS question,
        q_lang_answer.content AS answer,
        cat_lang_name.content AS category_name
    FROM webby_knowledgebase_questions q
    LEFT JOIN webby_knowledgebase_questions_categories qc ON q.id = qc.question_id
    LEFT JOIN webby_knowledgebase_multi_lang cat_lang_name 
        ON qc.category_id = cat_lang_name.foreign_id 
        AND cat_lang_name.model = 'pjCategory' 
        AND cat_lang_name.field = 'name'
    LEFT JOIN webby_knowledgebase_multi_lang q_lang_question 
        ON q.id = q_lang_question.foreign_id 
        AND q_lang_question.model = 'pjQuestion' 
        AND q_lang_question.field = 'question'
    LEFT JOIN webby_knowledgebase_multi_lang q_lang_answer 
        ON q.id = q_lang_answer.foreign_id 
        AND q_lang_answer.model = 'pjQuestion' 
        AND q_lang_answer.field = 'answer'
";

// Add WHERE clause only if keywords exist
if (!empty($escaped_keywords)) {
    $sql .= "
        WHERE (
            q_lang_question.content LIKE '%$escaped_keywords%' OR
            q_lang_answer.content LIKE '%$escaped_keywords%' OR
            cat_lang_name.content LIKE '%$escaped_keywords%'
        )
    ";
}

$sql .= " ORDER BY q.id DESC";

$result = $mysqli->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Error executing query: ' . $mysqli->error]);
    exit;
}

$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = [
        'id' => (int) $row['question_id'],
        'question' => $row['question'],
        'answer' => $row['answer'],
        'category' => $row['category_name'],
        'views' => (int) $row['views'],
        'created' => $row['created'],
    ];
}

// Close connection
$mysqli->close();

// Return results
echo json_encode(['data' => $questions], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
