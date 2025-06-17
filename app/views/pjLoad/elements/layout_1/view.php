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
	
	<div class="kb-view-container">
		<?php
		if(isset($tpl['arr']))
		{ 
			?>
			<h1 class="kb-title"><?php echo pjSanitize::html($tpl['arr']['question']); ?></h1>
			<?php
			$path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
			$path = $path == '/' ? '' : $path;
			
			$category_id = NULL;
			if (!empty($tpl['arr']['category_ids']))
			{
				$category_id = max($tpl['arr']['category_ids']);
			}
		
			$category_slug = array();
			if ($tpl['option_arr']['o_seo_url'] == 'No')
			{
				$category_slug[] = '<a href="'.$_SERVER['PHP_SELF'].'?controller=pjLoad&amp;action=pjActionIndex">'.__('label_home', true, false).'</a>';
			}else{
				$category_slug[] = '<a href="'.$path.'/home/">'.__('label_home', true, false).'<a/>';
			}
			if (!is_null($category_id))
			{
				$arr = array();
				pjUtil::getBreadcrumbTree($arr, $tpl['category_arr'], $category_id);
				krsort($arr);
				$arr = array_values($arr);
				
				foreach ($arr as $k => $category)
				{
					if ($tpl['option_arr']['o_seo_url'] == 'No')
					{
						$category_slug[] = '<a href="'.$_SERVER['PHP_SELF'].'?controller=pjLoad&amp;action=pjActionIndex&amp;category_id='.$category['data']['id'].'">'. $category['data']['name'].'</a>';
					}else{
						$category_slug[] = '<a href="'.$path.'/category/'.$category['data']['id'].'/'.$controller->friendlyURL($category['data']['name']).'.html">'.$category['data']['name'].'</a>';
					}
				}
			}
			
			$category_arr = array();
			if(!is_null($tpl['arr']['category_ids'])){
				foreach ($tpl['category_arr'] as $category){
					if(in_array($category['data']['id'], $tpl['arr']['category_ids'])){
						if ($tpl['option_arr']['o_seo_url'] == 'No')
						{
							$category_arr[] = '<a href="'.$_SERVER['PHP_SELF'].'?controller=pjLoad&amp;action=pjActionIndex&amp;category_id='.$category['data']['id'].'">'. $category['data']['name'] .'</a>';
						}else{
							$category_arr[] = '<a href="'.$path.'/category/'.$category['data']['id'].'/'.$controller->friendlyURL($category['data']['name']).'.html">'.$category['data']['name'].'</a>';
						}
					}
				}
			}
			$detail_url = pjUtil::getCurrentURL();
			?>
			<div class="kb-breadcrumb"><?php echo join("<abbr></abbr>", $category_slug);?></div>
			<div class="kb-detail">
				<div class="kb-stats">
					<div class="kb-inner-stats">
						<label><span><?php __('label_added', false, true);?></span>: <?php echo pjUtil::formatDate(date('Y-m-d', strtotime($tpl['arr']['created'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', strtotime($tpl['arr']['created'])), 'H:i:s', $tpl['option_arr']['o_time_format']);?></label>
						<label><span><?php __('label_last_updated', false, true);?></span>: <?php echo pjUtil::formatDate(date('Y-m-d', strtotime($tpl['arr']['modified'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', strtotime($tpl['arr']['modified'])), 'H:i:s', $tpl['option_arr']['o_time_format']);?></label>
						<label><span><?php __('label_author', false, true);?></span>: <?php echo pjSanitize::clean($tpl['arr']['name']);?></label>
						<label><span><?php __('label_category', false, true);?></span>: <?php echo !empty($category_arr) ? join(", ", $category_arr) : null;?></label>
						<a class="kb-sharing kb-printer" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjLoad&amp;action=pjActionPreview&amp;id=<?php echo $tpl['arr']['id'];?>" target="_blank"><?php __('label_print_article', false, true);?></a>
						<a id="kb_email_<?php echo $hash; ?>" class="kb-sharing kb-email" href="javascript:void(0);" rev="<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjLoad&action=pjActionSharing&id=<?php echo $tpl['arr']['id'];?>" rel="<?php echo $detail_url;?>"><?php __('label_email_to_friend', false, true);?></a>
					</div>
				</div>
				<div class="kb-rating">
					<label><?php __('label_average_rating', false, true);?></label>
					<abbr id="avg_rating_<?php echo $hash;?>"><?php echo number_format($tpl['arr']['avg_rate'], 2); ?></abbr>
					<div class="rating-container">
						<form action="#" method="post">
							<input type="hidden" name="question_id" value="<?php echo $tpl['arr']['id'];?>">
							<span class="question-rating" >
								<select name="rate">
									<?php
									$avg = floor($tpl['arr']['avg_rate']);
									for ($j=1; $j<=5; $j++) {
										?>
										<option value="<?php echo $j;?>" <?php echo ($avg == $j) ? 'selected="selected"' : '';?>><?php echo $j;?></option>
										<?php
									}
									?>
								</select>
							</span>
							<span id="rating_message_<?php echo $hash;?>" class="rating-label"></span>
						</form>
					</div>
				</div>
			</div>
			<div class="kb-answer">
				<?php echo stripslashes($tpl['arr']['answer']);?>
			</div>
			<div id="kb_preview_container" class="kb-preview-container">
				<h1 class="kb-title"><?php echo pjSanitize::html($tpl['arr']['question']); ?></h1>
				<div class="kb-answer">
					<?php echo stripslashes($tpl['arr']['answer']);?>
				</div>
			</div>
			<?php
		}else{
			__('front_there_is_no_question');
		} 
		?>
	</div>
	<div id="kb_email_dialog" title="<?php __('label_share_article');?>" style="display:none;"></div>
</div>