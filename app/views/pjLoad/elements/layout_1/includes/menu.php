<?php
$home_url = $_SERVER['PHP_SELF'] . "?controller=pjLoad&amp;action=pjActionIndex";
$category_url = $_SERVER['PHP_SELF'] . "?controller=pjLoad&amp;action=pjActionCategory";
$glossary_url = $_SERVER['PHP_SELF'] . "?controller=pjLoad&amp;action=pjActionGlossary";
if ($tpl['option_arr']['o_seo_url'] == 'Yes')
{
	$path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
	$path = $path == '/' ? '' : $path;
	$category_url = $path . '/category/';
	$home_url = $path . '/home/';
	$glossary_url = $path . '/glossary/';
} 
?>
<div class="kb-menu-container">
	<a href="<?php echo $home_url; ?>" <?php echo $_GET['action'] == 'pjActionIndex' || $_GET['action'] == 'pjActionPreview' || $_GET['action'] == 'pjActionView' ? 'class="kb-menu-focus"' : null;?>><?php __('label_home', false, true);?></a>
	<a href="<?php echo $category_url; ?>" <?php echo $_GET['action'] == 'pjActionCategory' ? 'class="kb-menu-focus"' : null;?>><?php __('label_categories', false, true);?></a>
	<?php
	if($tpl['option_arr']['o_show_glossary'] == 'Yes'){
		?><a href="<?php echo $glossary_url; ?>" <?php echo $_GET['action'] == 'pjActionGlossary' ? 'class="kb-menu-focus"' : null;?>><?php __('label_glossary', false, true);?></a><?php
	} 
	?>
</div>