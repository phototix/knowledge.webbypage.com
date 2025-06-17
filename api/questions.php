<?php
require '../app/config/config.inc.php'; // Bootstrap app
require '../app/controllers/pjAppController.controller.php'; // Base controller

use pjQuestionModel;
use pjMultiLangModel;
use pjQuestionCategoryModel;

header("Content-Type: application/json");

$localeId = (isset($_GET['locale_id']) && is_numeric($_GET['locale_id'])) ? $_GET['locale_id'] : 1;
$questionModel = pjQuestionModel::factory()
    ->join('pjMultiLang', "t2.model='pjQuestion' AND t2.foreign_id=t1.id AND t2.locale='$localeId' AND t2.field='question'", 'left outer')
    ->join('pjMultiLang', "t3.model='pjQuestion' AND t3.foreign_id=t1.id AND t3.locale='$localeId' AND t3.field='answer'", 'left outer')
    ->join('pjUser', "t4.id=t1.user_id", 'left outer');

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $q = pjObject::escapeString($_GET['q']);
    $questionModel->where('t2.content LIKE', "%$q%")->orWhere('t3.content LIKE', "%$q%");
}
if (isset($_GET['question']) && !empty($_GET['question'])) {
    $q = pjObject::escapeString($_GET['question']);
    $questionModel->where('t2.content LIKE', "%$q%");
}
if (isset($_GET['answer']) && !empty($_GET['answer'])) {
    $q = pjObject::escapeString($_GET['answer']);
    $questionModel->where('t3.content LIKE', "%$q%");
}
if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $user_id = pjObject::escapeString($_GET['user_id']);
    $questionModel->where('t1.user_id', $user_id);
}
if (isset($_GET['category_id']) && !empty($_GET['category_id'])) {
    $category_id = pjObject::escapeString($_GET['category_id']);
    $questionModel->where("(t1.id IN (SELECT question_id FROM `" . pjQuestionCategoryModel::factory()->getTable() . "` WHERE category_id = $category_id))");
}
if (isset($_GET['status']) && in_array($_GET['status'], ['T', 'F'])) {
    $questionModel->where('t1.status', $_GET['status']);
}

$column = isset($_GET['column']) ? $_GET['column'] : 'created';
$direction = isset($_GET['direction']) && in_array(strtoupper($_GET['direction']), ['ASC', 'DESC']) ? strtoupper($_GET['direction']) : 'DESC';

$total = $questionModel->findCount()->getData();
$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $rowCount;
$pages = ceil($total / $rowCount);
if ($page > $pages) $page = $pages;

$data = $questionModel
    ->select("t1.*, t2.content as question, t3.content as answer, t4.name as author")
    ->orderBy("$column $direction")
    ->limit($rowCount, $offset)
    ->findAll()
    ->getData();

// Fetch categories for each question (optional but useful)
foreach ($data as &$item) {
    $categories = pjQuestionCategoryModel::factory()
        ->join('pjCategory', 'pjCategory.id=t1.category_id', 'inner')
        ->where('t1.question_id', $item['id'])
        ->select('pjCategory.id, pjCategory.name')
        ->findAll()
        ->getData();
    $item['categories'] = $categories;
}

echo json_encode([
    'data' => $data,
    'total' => $total,
    'pages' => $pages,
    'page' => $page,
    'rowCount' => $rowCount,
    'column' => $column,
    'direction' => $direction,
]);
exit;
