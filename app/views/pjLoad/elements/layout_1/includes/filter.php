<?php
$path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$path = $path == '/' ? '' : $path; 
?>
<input type="hidden" id="seo_url_<?php echo $hash?>" name="seo_url" value="<?php echo $tpl['option_arr']['o_seo_url'];?>" />
<input type="hidden" id="clone_seo_url_<?php echo $hash?>" name="clone_seo_url" value="<?php echo $path . '/search{sortby}{keyword}{category}';?>" />
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjLoad&amp;action=pjActionIndex" method="get" id="frmKBSearch_<?php echo $hash;?>" >
	<input type="hidden" name="controller" value="pjLoad" />
	<input type="hidden" name="action" value="pjActionIndex" />
	<input type="hidden" name="kb_search" value="1" />
	<input type="hidden" id="sortby_<?php echo $hash;?>" name="sortby" value="<?php echo isset($_GET['sortby']) && $_GET['sortby'] != '' ? pjSanitize::clean($_GET['sortby']) : null; ?>" />

	<div class="kb-search-container">
		<div class="search-box">
			<abbr></abbr>
			<span>
				<input name="keyword" id="keyword_<?php echo $hash;?>" class="kb-text" value="<?php echo isset($_GET['keyword']) && $_GET['keyword'] != '' ? pjSanitize::clean($_GET['keyword']) : null;?>" placeholder="<?php __('label_search', false, true);?>" />
			</span>
		</div>
		<div class="category-box">
			<select id="category_id_<?php echo $hash;?>" name="category_id" class="kb-select">
				<option value="">-- <?php __('label_choose');?> --</option>
				<?php
				foreach ($tpl['category_arr'] as $category)
				{
					?><option value="<?php echo $category['data']['id']; ?>" <?php echo isset($_GET['category_id']) && $_GET['category_id'] == $category['data']['id'] ? 'selected="selected"' : null;?>><?php echo str_repeat("-----", $category['deep']) . " " .pjSanitize::html($category['data']['name']); ?></option><?php
				}
				?>
			</select>
		</div>
	</div>
	<div class="kb-filter-tab">
		<?php
		if($_GET['action'] != 'pjActionView' && $_GET['action'] != 'pjActionCategory'){
			?>
			<div class="kb-normal-tab">
				<a href="javascript:void(0);" class="kb-tab-item<?php echo (isset($_GET['sortby']) && $_GET['sortby'] == 'featured') ? ' kb-tab-focus' : null;?>" rev="featured"><?php __('label_featured', false, true);?></a>
				<a href="javascript:void(0);" class="kb-tab-item<?php echo isset($_GET['sortby']) && $_GET['sortby'] == 'views' ? ' kb-tab-focus' : null;?>" rev="views"><?php __('label_most_popular', false, true);?></a>
				<a href="javascript:void(0);" class="kb-tab-item<?php echo isset($_GET['sortby']) && $_GET['sortby'] == 'created' ? ' kb-tab-focus' : null;?>" rev="created"><?php __('label_recently_added', false, true);?></a>
			</div>
			<div class="kb-narrow-tab">
				<a href="javascript:void(0);" class="kb-label-tab">
					<span>
						<?php 
						if((isset($_GET['sortby']) && $_GET['sortby'] == 'featured') || !isset($_GET['sortby'])){
							__('label_featured', false, true);
						}else if(isset($_GET['sortby']) && $_GET['sortby'] == 'views'){
							__('label_most_popular', false, true);
						}else if(isset($_GET['sortby']) && $_GET['sortby'] == 'created'){
							__('label_recently_added', false, true);
						}
						?>
					</span>
					<abbr></abbr>
				</a>
				<div class="kb-toggle-tab">
					<?php 
					if((isset($_GET['sortby']) && $_GET['sortby'] == 'featured') || !isset($_GET['sortby'])){
						?>
						<a href="javascript:void(0);" class="kb-tab-item" rev="views"><?php __('label_most_popular', false, true);?></a>
						<a href="javascript:void(0);" class="kb-tab-item" rev="created"><?php __('label_recently_added', false, true);?></a>
						<?php 
					}else if(isset($_GET['sortby']) && $_GET['sortby'] == 'views'){
						?>
						<a href="javascript:void(0);" class="kb-tab-item" rev="featured"><?php __('label_featured', false, true);?></a>
						<a href="javascript:void(0);" class="kb-tab-item" rev="created"><?php __('label_recently_added', false, true);?></a>
						<?php 
					}else if(isset($_GET['sortby']) && $_GET['sortby'] == 'created'){
						?>
						<a href="javascript:void(0);" class="kb-tab-item" rev="featured"><?php __('label_featured', false, true);?></a>
						<a href="javascript:void(0);" class="kb-tab-item" rev="views"><?php __('label_most_popular', false, true);?></a>
						<?php 
					}
					?>
					
					
				</div>
			</div>
			<?php
		} else{
			if (@$_GET['controller'] == 'pjLoad' && @$_GET['action'] == 'pjActionView')
			{
				$back = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['PHP_SELF'] .'?controller=pjLoad&amp;action=pjActionIndex'. (isset($_GET['iframe']) ? '&amp;iframe' : NULL);
				?>
				<div class="kb-back"><a href="<?php echo $back;?>"><abbr class="left"></abbr><abbr class="middle"><?php __('label_back', false, true);?></abbr><abbr class="right"></abbr></a></div>
				<?php
			}
			if (@$_GET['controller'] == 'pjLoad' && @$_GET['action'] == 'pjActionCategory')
			{
				?><div class="kb-category-heading"><?php __('label_categories', false, true);?></div><?php
			}
		}
		?>
	</div>
</form>