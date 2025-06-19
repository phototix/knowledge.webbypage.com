<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load database config
require_once 'config/database.php';

// Database connection
$mysqli = new mysqli($db_host, $db_user, $db_pass, 'webbycms_knowledge');

// Check connection
if ($mysqli->connect_error) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

// SQL to retrieve questions, answers, and category names
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
    ORDER BY q.id DESC
";

$result = $mysqli->query($sql);

if (!$result) {
    die('Error executing query: ' . $mysqli->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Knowledgebase Questions</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.4.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 2rem;
        }
        table {
            font-size: 0.95rem;
        }
        th, td {
            vertical-align: top;
        }
        .table-responsive {
            margin-top: 1rem;
        }
    </style>
</head>
<body>

<h2 class="mb-4">Knowledgebase Questions</h2>

<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Question</th>
                <th>Answer</th>
                <th>Category</th>
                <th>Views</th>
                <th>Created</th>
                <th>Modified</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['question_id']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($row['question'])); ?></td>
                <td><?php echo nl2br(htmlspecialchars($row['answer'])); ?></td>
                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                <td><?php echo htmlspecialchars($row['views']); ?></td>
                <td><?php echo htmlspecialchars($row['created']); ?></td>
                <td><?php echo htmlspecialchars($row['modified']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap 5 JS Bundle CDN (with Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.4.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
// Close connection
$mysqli->close();
?>
