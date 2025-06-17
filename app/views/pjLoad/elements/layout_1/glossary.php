<div id="kb_main_container_<?php echo $hash;?>" class="kb-main-container">
	<div class="kb-heading">
		<?php
		include PJ_VIEWS_PATH . 'pjLoad/elements/layout_1/includes/locale.php';
		include PJ_VIEWS_PATH . 'pjLoad/elements/layout_1/includes/menu.php'; 
		?>
	</div>
	<div class="kb-glossary-alphabet">
		<?php
		$path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
		$path = $path == '/' ? '' : $path;
		if ($tpl['option_arr']['o_seo_url'] == 'No')
		{ 
			?><a class="kb-alphabet<?php echo !isset($_GET['letter']) ? ' kb-alphabet-focus' : null;?>" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjLoad&amp;action=pjActionGlossary"><?php __('label_all', false, true);?></a><?php
		}else{
			?><a class="kb-alphabet<?php echo !isset($_GET['letter']) ? ' kb-alphabet-focus' : null;?>" href="<?php echo $path . '/glossary/'; ?>"><?php __('label_all', false, true);?></a><?php
		}
		foreach(range('A','Z') as $i) {
			if ($tpl['option_arr']['o_seo_url'] == 'No')
			{
				?><a class="kb-alphabet<?php echo isset($_GET['letter']) && $_GET['letter'] == $i ? ' kb-alphabet-focus' : null;?>" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjLoad&amp;action=pjActionGlossary&amp;letter=<?php echo $i;?>"><?php echo $i;?></a><?php
			}else{
				
				?><a class="kb-alphabet<?php echo isset($_GET['letter']) && $_GET['letter'] == $i ? ' kb-alphabet-focus' : null;?>" href="<?php echo $path . '/glossary/' . $i . '/'; ?>"><?php echo $i;?></a><?php
			}
		} 
		?>
	</div>
	
	<div id="kb_glossary_container_<?php echo $hash;?>" class="kb-glossary-list">
		<?php
		if(!empty($tpl['arr'])){
			foreach($tpl['arr'] as $v){
				?>
				<div class="kb-glossary-box">
					<div class="word"><?php echo pjSanitize::clean($v['word']);?></div>
					<div class="description"><?php echo stripslashes($v['description']);?></div>
				</div>
				<?php
			}
		} else{
			?><div class="kb-no-glossary"><?php __('lable_no_glossary', false, true);?></div><?php
		}
		?>
	</div>
</div>