<?php
$hash = rand(1000,9999);

switch ($tpl['option_arr']['o_layout']) {
	case 'layout_1':
		include_once PJ_VIEWS_PATH . 'pjLoad/elements/layout_1/glossary.php';
		break;
	case 'layout_2':
		include_once PJ_VIEWS_PATH . 'pjLoad/elements/layout_2/glossary.php';
		break;
	case 'layout_3':
		include_once PJ_VIEWS_PATH . 'pjLoad/elements/layout_3/glossary.php';
		break;
	default:
		include_once PJ_VIEWS_PATH . 'pjLoad/elements/layout_1/glossary.php';
		break;
}
include_once PJ_VIEWS_PATH . 'pjLoad/elements/loadscript.php';
?>