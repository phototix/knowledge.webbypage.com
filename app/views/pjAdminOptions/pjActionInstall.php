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
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	?>
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1"><?php __('lblInstall'); ?></a></li>
			<li><a href="#tabs-2"><?php __('lblOptional'); ?></a></li>
		</ul>
		<div id="tabs-1">
			<?php pjUtil::printNotice(NULL, __('lblInstallPhp1Title', true), false, false); ?>
			
			<p style="margin: 0 0 10px; font-weight: bold"><?php __('lblInstallPhp1_1'); ?></p>
			<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:50px">
&lt;?php
ob_start();
?&gt;</textarea>
			<p style="margin: 20px 0 10px; font-weight: bold"><?php __('lblInstallPhp1_2'); ?></p>
			<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:30px">{KB_LOAD}</textarea>
			<p style="margin: 20px 0 10px; font-weight: bold"><?php __('lblInstallPhp1_3'); ?></p>
			<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:30px">
&lt;?php include '<?php echo dirname($_SERVER['SCRIPT_FILENAME']); ?>/app/views/pjLayouts/pjActionLoad.php'; ?&gt;</textarea>
		</div>
		<div id="tabs-2">
			<?php pjUtil::printNotice(NULL, __('lblInstallPhp2Title', true), false, false); ?>
			
			<form action="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminOptions&amp;action=pjActionUpdate" method="post" class="form pj-form b20">
				<input type="hidden" name="options_update" value="1" />
				<input type="hidden" name="next_action" value="pjActionInstall" />
				<input type="hidden" name="tab_id" value="tabs-2" />
				<?php
				foreach ($tpl['o_arr'] as $item)
				{
					if ($item['key'] == 'o_seo_url')
					{
						?>
						<p>
							<label class="float_left w150 pt5"><?php __('opt_' . $item['key']); ?></label>
							<span class="inline-block">
								<select name="value-<?php echo $item['type']; ?>-<?php echo $item['key']; ?>" class="pj-form-field float_left">
								<?php
								$default = explode("::", $item['value']);
								$enum = explode("|", $default[0]);
								
								$enumLabels = array();
								if (!empty($item['label']) && strpos($item['label'], "|") !== false)
								{
									$enumLabels = explode("|", $item['label']);
								}
								
								foreach ($enum as $k => $el)
								{
									if ($default[1] == $el)
									{
										?><option value="<?php echo $default[0].'::'.$el; ?>" selected="selected"><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el); ?></option><?php
									} else {
										?><option value="<?php echo $default[0].'::'.$el; ?>"><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el); ?></option><?php
									}
								}
								?>
								</select>
								<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button float_left l5 align_middle" />
							</span>
						</p>
						<?php
						break;
					}
				}
				
				$listing_page = NULL;
				foreach ($tpl['o_arr'] as $item)
				{
					if ($item['key'] == 'o_install_page')
					{
						if(empty($item['value']) || $item['value']=='http://localhost/SCRIPTS/KnowledgeBase/testpage.php')
						{
							$listing_page = PJ_INSTALL_URL . 'preview.php';
						}else{
							$listing_page = $item['value'];
						}
						?>
						<p>
							<label class="float_left w320 pt5"><?php __('opt_' . $item['key']); ?></label>
							<span class="pj-form-field-custom pj-form-field-custom-before float_left">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-url"></abbr></span>
								<input type="text" name="value-<?php echo $item['type']; ?>-<?php echo $item['key']; ?>" class="pj-form-field w250" value="<?php echo htmlspecialchars(stripslashes($listing_page)); ?>" />
							</span>
							<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button float_left l5 align_middle" />
						</p>
						<?php
						break;
					}
				}
				?>
			</form>
			
			<?php
			$parts = parse_url($listing_page);
			$prefix = NULL;
			if (substr($parts['path'], -1) !== "/")
			{
				$prefix = basename($parts['path']);
			}
			if (isset($parts['query']) && !empty($parts['query']))
			{
				$prefix .= "?" . $parts['query'];
			}
			$prefix .= (strpos($prefix, "?") === false) ? "?" : "&";
			?>
			<p style="margin: 0 0 10px; font-weight: bold"><?php __('lblInstallPhp1_4'); ?></p>
			<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:380px">
RewriteEngine On
RewriteRule home/ <?php echo $prefix; ?>controller=pjLoad&action=pjActionIndex [L,NC]
RewriteRule search/sortby/(\S+)/keyword/(\S+)/category/(\d+)/ <?php echo $prefix; ?>controller=pjLoad&action=pjActionIndex&kb_search=1&sortby=$1&keyword=$2&category_id=$3 [L,NC]
RewriteRule search/sortby/(\S+)/keyword/(\S+)/ <?php echo $prefix; ?>controller=pjLoad&action=pjActionIndex&kb_search=1&sortby=$1&keyword=$2&category_id= [L,NC]
RewriteRule search/sortby/(\S+)/category/(\d+)/ <?php echo $prefix; ?>controller=pjLoad&action=pjActionIndex&kb_search=1&sortby=$1&keyword=&category_id=$2 [L,NC]
RewriteRule search/sortby/(\S+)/ <?php echo $prefix; ?>controller=pjLoad&action=pjActionIndex&kb_search=1&sortby=$1&keyword=&category_id= [L,NC]
RewriteRule search/category/(\d+)/ <?php echo $prefix; ?>controller=pjLoad&action=pjActionIndex&kb_search=1&sortby=&keyword=&category_id=$1 [L,NC]
RewriteRule search/keyword/(\S+)/category/(\d+)/ <?php echo $prefix; ?>controller=pjLoad&action=pjActionIndex&kb_search=1&sortby=&keyword=$1&category_id=$2 [L,NC]
RewriteRule search/keyword/(\S+)/ <?php echo $prefix; ?>controller=pjLoad&action=pjActionIndex&kb_search=1&sortby=&keyword=$1&category_id= [L,NC]
RewriteRule search/ <?php echo $prefix; ?>controller=pjLoad&action=pjActionIndex&kb_search=1&sortby=&keyword=&category_id= [L,NC]
RewriteRule category/(\d+)/(\S+)/(\S+).html$ <?php echo $prefix; ?>controller=pjLoad&action=pjActionView&id=$1 [L,NC]
RewriteRule category/(\d+)/(\S+).html$ <?php echo $prefix; ?>controller=pjLoad&action=pjActionIndex&category_id=$1 [L,NC]
RewriteRule category/ <?php echo $prefix; ?>controller=pjLoad&action=pjActionCategory [L,NC]
RewriteRule glossary/(\S+)/ <?php echo $prefix; ?>controller=pjLoad&action=pjActionGlossary&letter=$1 [L,NC]
RewriteRule glossary/ <?php echo $prefix; ?>controller=pjLoad&action=pjActionGlossary [L,NC]</textarea>
			<p style="margin: 20px 0 10px; font-weight: bold"><?php __('lblInstallPhp1_5'); ?></p>
			<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:35px">
&lt;base href="<?php echo $listing_page; ?>" /&gt;</textarea>
			
		</div>
	</div>
	<?php
	if (isset($_GET['tab_id']) && !empty($_GET['tab_id']))
	{
		$tab_id = explode("-", $_GET['tab_id']);
		$tab_id = (int) $tab_id[1] - 1;
		$tab_id = $tab_id < 0 ? 0 : $tab_id;
		?>
		<script type="text/javascript">
		(function ($) {
			$(function () {
				$("#tabs").tabs("option", "selected", <?php echo $tab_id; ?>);
			});
		})(jQuery_1_8_2);
		</script>
		<?php
	}
}
?>