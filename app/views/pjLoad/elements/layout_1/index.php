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
	
	<div class="kb-question-list">
		<?php
		if(!empty($tpl['arr']))
		{
			foreach($tpl['arr'] as $v)
			{
				$category_seo = '';
				$category_arr = array();
				if(!is_null($v['category_ids'])){
					foreach ($tpl['category_arr'] as $category){
						if(in_array($category['data']['id'], $v['category_ids'])){
							$category_arr[] = $category['data']['name'];
							$category_seo = '/' . $controller->friendlyURL($category['data']['name']);
						}
					}
				}
				if ($tpl['option_arr']['o_seo_url'] == 'No')
				{
					$detail_url = $_SERVER['SCRIPT_NAME'] . '?controller=pjLoad&amp;action=pjActionView&amp;id=' . $v['id'] .(isset($_GET['iframe']) ? '&amp;iframe' : NULL);
				} else {
					$path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
					$path = $path == '/' ? '' : $path;
					$detail_url = $path .'/category/' . $v['id'] . $category_seo . '/'. $controller->friendlyURL($v['question']) . ".html";
				}
				?>
				<div class="kb-question-box" lang="<?php echo $v['id']; ?>">
					<a id="kb_view_detail_<?php echo $v['id'];?>" class="view" href="<?php echo $detail_url;?>"><span><?php echo strtolower(__('label_view', true, false));?></span><abbr></abbr></a>
					<div class="question-detail">
						<div class="heading">
							<label><?php echo pjSanitize::clean($v['question'])?></label>
							<div class="rating-container">
								<form action="#" method="post">
									<input type="hidden" name="question_id" value="<?php echo $v['id'];?>">
									<span class="question-rating" >
										<select name="rate">
											<?php
											$avg = floor($v['avg_rate']);
											for ($j=1; $j<=5; $j++) {
												?>
												<option value="<?php echo $j;?>" <?php echo ($avg == $j) ? 'selected="selected"' : '';?>><?php echo $j;?></option>
												<?php
											}
											?>
										</select>
									</span>
								</form>
							</div>
						</div>
						<div class="detail">
							<label><?php echo pjUtil::formatDate(date('Y-m-d', strtotime($v['created'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', strtotime($v['created'])), 'H:i:s', $tpl['option_arr']['o_time_format']); ?></label><abbr></abbr>
							<label><span><?php echo __('label_by', false, true);?></span><?php echo pjSanitize::clean($v['name']);?></label><abbr></abbr>
							<label><span><?php echo __('label_in', false, true);?></span><?php echo !empty($category_arr) ? join(", ", $category_arr) : null;?></label>
						</div>
					</div>
				</div>
				<?php
			}
			include PJ_VIEWS_PATH . 'pjLoad/elements/layout_1/includes/paginator.php';
		} else {
			?><div class="kb-no-question"><?php __('lable_no_question', false, true);?></div><?php
		}
		?>
	</div>
</div>