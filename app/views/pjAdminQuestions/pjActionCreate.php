<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminQuestions&amp;action=pjActionIndex"><?php __('menuQuestions'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminQuestions&amp;action=pjActionCreate"><?php __('lblAddQuestion'); ?></a></li>
			<?php
			if($controller->isAdmin())
			{ 	
				?><li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] != 'pjAdminCategories' ? NULL : $active; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionIndex"><?php __('menuCategories'); ?></a></li><?php
			} 
			?>
		</ul>
	</div>
	<?php pjUtil::printNotice(__('infoAddQuestionTitle', true, false), __('infoAddQuestionBody', true, false));?>
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
	<div class="multilang"></div>
	<?php endif;?>
	
	<div class="clear_both">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminQuestions&amp;action=pjActionCreate" method="post" id="frmCreateQuestion" class="form pj-form" autocomplete="off">
			<input type="hidden" name="question_create" value="1" />
			
			<p class="chosen">
				<label class="title110"><?php __('lblCategory'); ?></label>
				<select name="category_id[]" id="category_id" class="pj-form-field required w400" multiple="multiple" data-placeholder="-- <?php __('lblChoose'); ?> --">
				<?php
				foreach ($tpl['category_arr'] as $category)
				{
					?><option value="<?php echo $category['data']['id']; ?>"><?php echo str_repeat("-----", $category['deep']) . " " .pjSanitize::html($category['data']['name']); ?></option><?php
				}
				?>
				</select>
			</p>
			<?php
			if($controller->isAdmin())
			{ 
				?>
				<p class="chosen">
					<label class="title110"><?php __('lblAuthor'); ?></label>
					<span class="block">
						<select name="user_id" id="user_id" class="pj-form-field w300 required">
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
			foreach ($tpl['lp_arr'] as $v)
			{
				?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title110"><?php __('lblQuestion'); ?></label>
					<span class="inline_block">
						<textarea name="i18n[<?php echo $v['id']; ?>][question]" class="pj-form-field w500 h100<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>"></textarea>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif;?>
					</span>
				</p>
				<?php
			}
			foreach ($tpl['lp_arr'] as $v)
			{
				?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title110"><?php __('lblAnswer'); ?></label>
					<span class="inline_block">
						<textarea name="i18n[<?php echo $v['id']; ?>][answer]" class="mceEditor" style="width: 570px; height: 200px"></textarea>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input float_right"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif;?>
						<br class="clear_both" />
					</span>
				</p>
				<?php
			}
			?>
			<p>
				<label class="title110"><?php __('lblMarkAsFeatured'); ?></label>
				<span class="inline_block">
					<select name="featured" id="featured" class="pj-form-field">
						<?php
						foreach (__('_yesno', true) as $k => $v)
						{
							?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<p>
				<label class="title110"><?php __('lblStatus'); ?></label>
				<span class="inline_block">
					<select name="status" id="status" class="pj-form-field required">
						<option value="">-- <?php __('lblChoose'); ?>--</option>
						<?php
						foreach (__('u_statarr', true) as $k => $v)
						{
							?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<p>
				<label class="title110">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
			</p>
		</form>
	</div>
	
	<script type="text/javascript">
	var pjLocale = pjLocale || {};
	pjLocale.langs = <?php echo $tpl['locale_str']; ?>;
	pjLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: pjLocale.langs,
				flagPath: pjLocale.flagPath,
				tooltip: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet faucibus enim.",
				select: function (event, ui) {
					
				}
			});
		});
	})(jQuery_1_8_2);
	</script>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	
	</script>
	<?php
}
?>