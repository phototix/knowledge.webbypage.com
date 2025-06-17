<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 1:
			pjUtil::printNotice($status[1]);
			break;
		case 2:
			pjUtil::printNotice($status[2]);
			break;
		case 9:
			pjUtil::printNotice($status[9]);
			break;
	}
} else {
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	?>
	
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminQuestions"><?php __('menuQuestions'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminQuestions&amp;action=pjActionCreate"><?php __('lblAddQuestion'); ?></a></li>
			<?php
			if($controller->isAdmin())
			{ 	
				?><li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] != 'pjAdminCategories' ? NULL : $active; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionIndex"><?php __('menuCategories'); ?></a></li><?php 
			}
			?>
		</ul>
	</div>
	
	<div class="b10">
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
			<button type="button" class="pj-button pj-button-detailed"><span class="pj-button-detailed-arrow"></span></button>
		</form>
		<?php
		$filter = __('filter', true);
		?>
		<div class="float_right t5">
			<a href="#" class="pj-button btn-all"><?php __('lblAll'); ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="T"><?php echo $filter['active']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="F"><?php echo $filter['inactive']; ?></a>
		</div>
		<br class="clear_both" />
	</div>
	<div class="pj-form-filter-advanced" style="display: none">
		<span class="pj-menu-list-arrow"></span>
		<form action="" method="get" class="form pj-form pj-form-search frm-filter-advanced">
			<div class="float_left w350">
				<p>
					<label class="title80"><?php __('lblQuestion'); ?></label>
					<span class="inline_block">
						<input type="text" name="question" id="question" class="pj-form-field w200" />
					</span>
				</p>
				
				<p>
					<label class="title80"><?php __('lblAnswer'); ?></label>
					<span class="inline_block">
						<input type="text" name="answer" id="answer" class="pj-form-field w200" />
					</span>
				</p>
			</div>
			<div class="float_right w350">
				<?php
				if($controller->isAdmin())
				{ 
					?>
					<p>
						<label class="title110"><?php __('lblAuthor'); ?></label>
						<span class="inline_block">
							<select name="user_id" id="user_id" class="pj-form-field w200">
								<option value="">-- <?php __('lblChoose'); ?>--</option>
								<?php
								foreach ($tpl['user_arr'] as $v)
								{
									?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['name']); ?></option><?php
								}
								?>
							</select>
						</span>
					</p>
					<?php
				} 
				?>
				<p>
					<label class="title110"><?php __('lblCategory'); ?></label>
					<select name="category_id" id="category_id" class="pj-form-field w200" data-placeholder="-- <?php __('lblChoose'); ?> --">
						<option value="">-- <?php __('lblChoose'); ?>--</option>
						<?php
						foreach ($tpl['category_arr'] as $category)
						{
							?><option value="<?php echo $category['data']['id']; ?>"><?php echo str_repeat("-----", $category['deep']) . " " .pjSanitize::html($category['data']['name']); ?></option><?php
						}
						?>
					</select>
				</p>
			</div>
			<br class="clear_both" />
			<p>
				<label class="title80">&nbsp;</label>
				<input type="submit" value="<?php __('btnSearch'); ?>" class="pj-button" />
				<input type="reset" value="<?php __('btnCancel'); ?>" class="pj-button" />
			</p>
		</form>
	</div>
	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	var myLabel = myLabel || {};
	myLabel.question = "<?php __('lblQuestion'); ?>";
	myLabel.author = "<?php __('lblAuthor'); ?>";
	myLabel.revert_status = "<?php __('revert_status', false, true); ?>";
	myLabel.exported = "<?php __('lblExport', false, true); ?>";
	myLabel.active = "<?php __('lblActive', false, true); ?>";
	myLabel.inactive = "<?php __('lblInActive', false, true); ?>";	
	myLabel.delete_selected = "<?php __('delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation'); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	</script>
	<?php
}
?>