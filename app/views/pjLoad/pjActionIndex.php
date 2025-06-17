<?php
$hash = rand(1000,9999);

switch ($tpl['option_arr']['o_layout']) {
	case 'layout_1':
		include_once PJ_VIEWS_PATH . 'pjLoad/elements/layout_1/index.php';
		break;
	case 'layout_2':
		include_once PJ_VIEWS_PATH . 'pjLoad/elements/layout_2/index.php';
		break;
	case 'layout_3':
		include_once PJ_VIEWS_PATH . 'pjLoad/elements/layout_3/index.php';
		break;
	default:
		include_once PJ_VIEWS_PATH . 'pjLoad/elements/layout_1/index.php';
		break;
}
include_once PJ_VIEWS_PATH . 'pjLoad/elements/loadscript.php';
?>