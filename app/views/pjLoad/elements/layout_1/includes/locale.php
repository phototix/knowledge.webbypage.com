<div class="kb-locale-container">
	<?php
	if (isset($tpl['locale_arr']))
	{
		$cnt = count($tpl['locale_arr']);
		if ($cnt > 1)
		{
			?><ul class="kb-buttonset kb-locale"><?php
			foreach ($tpl['locale_arr'] as $k => $locale)
			{
				?>
				<li class="<?php echo $k > 0 ? ($k + 1 != $cnt ? NULL : 'kb-buttonset-last') : 'kb-buttonset-first'; ?>">
					<a class="<?php echo $controller->getLocaleId() == $locale['id'] ? 'kb-menu-locale-focus' : NULL; ?>" href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?controller=pjLoad&amp;action=pjActionSetLocale&amp;locale=<?php echo $locale['id']; ?><?php echo isset($_GET['iframe']) ? '&amp;iframe' : NULL; ?>">
						<img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/<?php echo $locale['file']; ?>" alt="" />
					</a>
				</li>
				<?php
			}
			?></ul><?php
		}
	}
	?>
</div>