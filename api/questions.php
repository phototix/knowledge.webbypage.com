<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set content type to JSON
header('Content-Type: application/json');

// Load database config
require_once 'config/database.php';

// Connect to database
$mysqli = new mysqli($db_host, $db_user, $db_pass, 'webbycms_knowledge');

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $mysqli->connect_error]);
    exit;
}

// Get 'tenant' from query param, fallback to 'webby_knowledgebase_'
$tenant = isset($_GET['tenant']) ? trim($_GET['tenant']) : '';
$tenant_prefix = 'webby_knowledgebase_'; // default

if (!empty($tenant)) {
    // Sanitize tenant name: allow only letters, numbers, underscore
    if (preg_match('/^[a-zA-Z0-9_]+$/', $tenant)) {
        $tenant_prefix = $mysqli->real_escape_string($tenant) . '_knowledgebase_';
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid tenant identifier.']);
        exit;
    }
}

// Get optional search keyword
$keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';
$escaped_keywords = $mysqli->real_escape_string($keywords);

// Compose SQL using dynamic prefix
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
    FROM {$tenant_prefix}questions q
    LEFT JOIN {$tenant_prefix}questions_categories qc ON q.id = qc.question_id
    LEFT JOIN {$tenant_prefix}multi_lang cat_lang_name 
        ON qc.category_id = cat_lang_name.foreign_id 
        AND cat_lang_name.model = 'pjCategory' 
        AND cat_lang_name.field = 'name'
    LEFT JOIN {$tenant_prefix}multi_lang q_lang_question 
        ON q.id = q_lang_question.foreign_id 
        AND q_lang_question.model = 'pjQuestion' 
        AND q_lang_question.field = 'question'
    LEFT JOIN {$tenant_prefix}multi_lang q_lang_answer 
        ON q.id = q_lang_answer.foreign_id 
        AND q_lang_answer.model = 'pjQuestion' 
        AND q_lang_answer.field = 'answer'
";

// Add WHERE clause for keywords
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

// Execute query
$result = $mysqli->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Error executing query: ' . $mysqli->error]);
    exit;
}

// Build response
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

// Close DB
$mysqli->close();

// Return JSON
echo json_encode(['data' => $questions], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
