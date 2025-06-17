<div class="kb-paging-container">
	<?php
	if (isset($tpl['paginator']) && $tpl['paginator']['pages'] > 1)
	{		
		$page = 1 ;
		$query_string = $_SERVER['QUERY_STRING'];
		if(empty($query_string)){
			$query_string = "controller=pjLoad&amp;action=pjActionIndex";
		}
		if(isset($_GET['pjPage'])){
			$page = $_GET['pjPage'];
			$query_string = str_replace("&pjPage=" . $page, "", $query_string);
		}
		?>
		<ul class="kb-paginator">
			<?php
			$stages = 3;
			$lastpage = $tpl['paginator']['pages'];
								
			if ($page > 1)
			{
				?><li><a class="kb-prev" href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $query_string;?>&amp;pjPage=<?php echo $page - 1; ?>" ></a></li><?php
			}
			?><li class="dot"><?php 
			if ($lastpage < 7 + ($stages * 2))
			{
				for ($counter = 1; $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
					{
						?><li><a href="javascript:void(0);" class="current"><abbr class="left"></abbr><abbr class="middle"><?php echo $counter; ?></abbr><abbr class="right"></abbr></a></li><?php
					}else{
						?><li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $query_string;?>&amp;pjPage=<?php echo $counter; ?>" ><abbr class="left"></abbr><abbr class="middle"><?php echo $counter; ?></abbr><abbr class="right"></abbr></a></li><?php
					}
				}
			} else if ($lastpage > 5 + ($stages * 2)){
				
				if($page < 1 + ($stages * 2))		
				{
					for ($counter = 1; $counter < 4 + ($stages * 2); $counter++)
					{
						if ($counter == $page){
							?><li><a href="javascript:void(0);" class="current"><abbr class="left"></abbr><abbr class="middle"><?php echo $counter; ?></abbr><abbr class="right"></abbr></a></li><?php
						}else{
							?><li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $query_string;?>&amp;pjPage=<?php echo $counter; ?>" ><abbr class="left"></abbr><abbr class="middle"><?php echo $counter; ?></abbr><abbr class="right"></abbr></a></li><?php
						}	
					}
					?>
					<li class="dot"><span>.</span></li>
					<li class="dot"><span>.</span></li>
					<li class="dot"><span>.</span></li>
					<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $query_string;?>&amp;pjPage=<?php echo $lastpage - 1; ?>" ><abbr class="left"></abbr><abbr class="middle"><?php echo $lastpage - 1; ?></abbr><abbr class="right"></abbr></a></li>
					<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $query_string;?>&amp;pjPage=<?php echo $lastpage; ?>" ><abbr class="left"></abbr><abbr class="middle"><?php echo $lastpage; ?></abbr><abbr class="right"></abbr></a></li>
					<?php
				}else if($lastpage - ($stages * 2) > $page && $page > ($stages * 2)){
					?>
					<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $query_string;?>&amp;pjPage=1" ><abbr class="left"></abbr><abbr class="middle">1</abbr><abbr class="right"></abbr></a></li>
					<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $query_string;?>&amp;pjPage=2" ><abbr class="left"></abbr><abbr class="middle">2</abbr><abbr class="right"></abbr></a></li>
					<li class="dot"><span>.</span></li>
					<li class="dot"><span>.</span></li>
					<li class="dot"><span>.</span></li>
					<?php
					for ($counter = $page - $stages; $counter <= $page + $stages; $counter++){
						if ($counter == $page)
						{
							?><li><a href="javascript:void(0);" class="current"><abbr class="left"></abbr><abbr class="middle"><?php echo $counter; ?></abbr><abbr class="right"></abbr></a></li><?php
						}else{
							?><li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $query_string;?>&amp;pjPage=<?php echo $counter; ?>" ><abbr class="left"></abbr><abbr class="middle"><?php echo $counter; ?></abbr><abbr class="right"></abbr></a></li><?php
						}
					}
					?>
					<li class="dot"><span>.</span></li>
					<li class="dot"><span>.</span></li>
					<li class="dot"><span>.</span></li>
					<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $query_string;?>&amp;pjPage=<?php echo $lastpage - 1; ?>" ><abbr class="left"></abbr><abbr class="middle"><?php echo $lastpage - 1; ?></abbr><abbr class="right"></abbr></a></li>
					<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $query_string;?>&amp;pjPage=<?php echo $lastpage; ?>" ><abbr class="left"></abbr><abbr class="middle"><?php echo $lastpage; ?></abbr><abbr class="right"></abbr></a></li>
					<?php
				}else{
					?>
					<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $query_string;?>&amp;pjPage=1" ><abbr class="left"></abbr><abbr class="middle">1</abbr><abbr class="right"></abbr></a></li>
					<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $query_string;?>&amp;pjPage=2" ><abbr class="left"></abbr><abbr class="middle">2</abbr><abbr class="right"></abbr></a></li>
					<li class="dot"><span>.</span></li>
					<li class="dot"><span>.</span></li>
					<li class="dot"><span>.</span></li>
					<?php
					for ($counter = $lastpage - (2 + ($stages * 2)); $counter <= $lastpage; $counter++)
					{
						if ($counter == $page)
						{
							?><li><a href="javascript:void(0);" class="current"><abbr class="left"></abbr><abbr class="middle"><?php echo $counter; ?></abbr><abbr class="right"></abbr></a></li><?php
						}else{
							?><li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $query_string;?>&amp;pjPage=<?php echo $counter; ?>" ><abbr class="left"></abbr><abbr class="middle"><?php echo $counter; ?></abbr><abbr class="right"></abbr></a></li><?php
						}
					}
				}	
			}
			if ($page < $counter - 1){
				 ?><li><a class="kb-next" href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $query_string;?>&amp;pjPage=<?php echo $page + 1; ?>" ></a></li><?php
			}
			?>
		</ul>
		<?php
	} 
	?>
</div>