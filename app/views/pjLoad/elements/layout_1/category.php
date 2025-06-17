<div id="kb_main_container_<?php echo $hash;?>" class="kb-main-container">
	<div class="kb-heading">
		<?php
		include PJ_VIEWS_PATH . 'pjLoad/elements/layout_1/includes/locale.php';
		include PJ_VIEWS_PATH . 'pjLoad/elements/layout_1/includes/menu.php'; 
		?>
	</div>
	<div class="kb-filter-container">
		<?php
		include PJ_VIEWS_PATH . 'pjLoad/elements/layout_1/includes/filter.php'; 
		?>
	</div>
	<div class="kb-category-list">
		<?php
		$path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
		$path = $path == '/' ? '' : $path;
	
		$i = 1;
		
		foreach($tpl['category_arr'] as $v){
			$arr = array();
			$category_slug = array();
			$category_id = $v['data']['id'];
			pjUtil::getBreadcrumbTree($arr, $tpl['category_arr'], $category_id);
			krsort($arr);
			$arr = array_values($arr);
			
			if(count($arr) > 1)
			{
				foreach ($arr as $k => $category)
				{
					if ($tpl['option_arr']['o_seo_url'] == 'No')
					{
						$category_slug[] = '<a href="'. $_SERVER['PHP_SELF']. '?controller=pjLoad&amp;action=pjActionIndex&amp;category_id='.$category['data']['id'].'">' . $category['data']['name'] . '</a>';
					}else{
						$category_slug[] = '<a href="'.$path.'/category/'.$category['data']['id'].'/'.$controller->friendlyURL($category['data']['name']).'.html">'.$category['data']['name'].'</a>';
					}
				}
			}
			$detail_arr = $tpl['arr'][$category_id];
			$slug = join("<abbr></abbr>", $category_slug);
			?>
			<div class="kb-category-box<?php echo $i % 2 == 0 ? ' kb-category-even-box' : ' kb-category-odd-box'?>">
				<div class="kb-innder-category-box">
					<?php
					if(count($arr) > 1)
					{
						 ?><label class="kb-category-title"><?php echo pjSanitize::html($v['data']['name']); ?></label><?php
					}else{
						if ($tpl['option_arr']['o_seo_url'] == 'No')
						{
							?>
							<label class="kb-category-title"><a href="<?php echo $_SERVER['PHP_SELF'];?>?controller=pjLoad&amp;action=pjActionIndex&amp;category_id=<?php echo $category_id;?>"><?php echo pjSanitize::html($v['data']['name']); ?></a></label>
							<?php
						}else{
							?>
							<label class="kb-category-title"><a href="<?php echo $path . '/category/' . $v['data']['id'] . '/' . $controller->friendlyURL($v['data']['name']). '.html';?>"><?php echo pjSanitize::html($v['data']['name']); ?></a></label>
							<?php
						}
					}
					?>
					<div class="kb-category-breadcrumb">
						<?php echo $slug;?>
					</div>
					<div class="kb-category-detail">
						<div class="kb-questions"><span><?php echo $detail_arr['cnt_questions'];?></span>&nbsp;<?php echo $detail_arr['cnt_questions'] != 1 ? __('label_questions', false, true) : __('label_question', false, true) ;?></div>
						<div class="kb-description"><?php echo pjSanitize::clean($detail_arr['description']); ?></div>
					</div>
				</div>
			</div>
			<?php
			$i++;
		}
		?>
	</div>
</div>