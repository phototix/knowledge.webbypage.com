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
}else{
	?>
	<div class="dashboard_header">
		<div class="item">
			<div class="stat questions">
				<div class="info">
					<abbr><?php echo $tpl['cnt_questions'];?></abbr>
					<?php echo (int) $tpl['cnt_questions'] != 1 ? strtolower(__('lblQuestions', true)) : strtolower(__('lblQuestion', true)); ?>
				</div>
			</div>
		</div>
		<div class="item">
			<div class="stat words">
				<div class="info">
					<abbr><?php echo $tpl['cnt_words'];?></abbr>
					<?php echo (int) $tpl['cnt_words'] != 1 ? strtolower(__('lblWords', true)) : strtolower(__('lblWord', true)); ?>
				</div>
			</div>
		</div>
		<div class="item">
			<div class="stat users">
				<div class="info">
					<abbr><?php echo $tpl['cnt_users'];?></abbr>
					<?php echo (int) $tpl['cnt_users'] != 1 ? strtolower(__('lblUsers', true)) : strtolower(__('lblUser', true)); ?>
				</div>
			</div>
		</div>
	</div>
	
	<div class="dashboard_box">
		<div class="dashboard_top">
			<div class="dashboard_column_top"><?php __('lblPopularQuestions');?></div>
			<div class="dashboard_column_top"><?php __('lblLastUpdatedQuestions');?></div>
			<div class="dashboard_column_top"><?php __('lblLastLoggedUsers');?></div>
		</div>
		<div class="dashboard_middle">
			<div class="dashboard_column">
				<?php
				$cnt = count($tpl['question_arr']);
				if ($cnt === 0)
				{
					?><p class="m10"><?php __('lblQuestionNotFound'); ?></p><?php
				}else{
					foreach ($tpl['question_arr'] as $k => $v)
					{
						?>
						<div class="dashboard_user_row dashboard_row<?php echo $k + 1 !== $cnt ? NULL : ' dashboard_row_last'; ?>">
							<label><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminQuestions&amp;action=pjActionUpdate&id=<?php echo $v['id']?>"><?php echo $v['question'];?></a></label>
							<label><?php echo $v['name'];?></label>
							<label><?php echo $v['views'] . " " . strtolower( $v['views'] != 1 ? __('lblViews', true, false) : __('lblView', true, false));?></label>
						</div>
						<?php
					}
				} 
				?>
			</div>
			<div class="dashboard_column">
				<?php
				$cnt = count($tpl['last_question_arr']);
				if ($cnt === 0)
				{
					?><p class="m10"><?php __('lblQuestionNotFound'); ?></p><?php
				}else{
					foreach ($tpl['last_question_arr'] as $k => $v)
					{
						?>
						<div class="dashboard_user_row dashboard_row<?php echo $k + 1 !== $cnt ? NULL : ' dashboard_row_last'; ?>">
							<label><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminQuestions&amp;action=pjActionUpdate&id=<?php echo $v['id']?>"><?php echo $v['question'];?></a></label>
							<label><?php echo $v['name'];?></label>
							<label><?php echo pjUtil::formatDate(date('Y-m-d', strtotime($v['modified'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', strtotime($v['modified'])), 'H:i:s', $tpl['option_arr']['o_time_format']); ?></label>
						</div>
						<?php
					}
				} 
				?>
			</div>
			<div class="dashboard_column">
				<?php
				$cnt = count($tpl['user_arr']);
				if ($cnt === 0)
				{
					?><p class="m10"><?php __('lblUserNotFound'); ?></p><?php
				}else{
					foreach ($tpl['user_arr'] as $k => $v)
					{
						?>
						<div class="dashboard_user_row dashboard_row<?php echo $k + 1 !== $cnt ? NULL : ' dashboard_row_last'; ?>">
							<?php
							if($controller->isAdmin()){
								?><label><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminUsers&amp;action=pjActionUpdate&id=<?php echo $v['id']?>"><?php echo $v['name'];?></a></label><?php
							} else{
								?><label><?php echo $v['name'];?></label><?php
							}
							?>
							<label><?php echo $v['email'];?></label>
							<label><?php echo pjUtil::formatDate(date('Y-m-d', strtotime($v['last_login'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', strtotime($v['last_login'])), 'H:i:s', $tpl['option_arr']['o_time_format']); ?></label>
						</div>
						<?php
					}
				} 
				?>
			</div>
		</div>
		<div class="dashboard_bottom"></div>
	</div>
	
	<div class="clear_left t20 overflow">
		<div class="float_left black t30 t20"><span class="gray"><?php echo ucfirst(__('lblDashLastLogin', true)); ?>:</span> <?php echo pjUtil::formatDate(date('Y-m-d', strtotime($_SESSION[$controller->defaultUser]['last_login'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', strtotime($_SESSION[$controller->defaultUser]['last_login'])), 'H:i:s', $tpl['option_arr']['o_time_format']); ?></div>
		<div class="float_right overflow">
		<?php
		list($hour, $day, $other) = explode("_", date("H:i_l_F d, Y"));
		$days = __('days', true, false);
		?>
			<div class="dashboard_date">
				<abbr><?php echo $days[date('w')]; ?></abbr>
				<?php echo pjUtil::formatDate(date('Y-m-d'), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s'), 'H:i:s', $tpl['option_arr']['o_time_format']); ?>
			</div>
			<div class="dashboard_hour"><?php echo $hour; ?></div>
		</div>
	</div>
	<?php
}
?>