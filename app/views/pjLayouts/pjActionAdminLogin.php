<!doctype html>
<html>
	<head>
		<title>Knowledge Base - WebbyPage</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<?php
		foreach ($controller->getCss() as $css)
		{
			echo '<link type="text/css" rel="stylesheet" href="'.(isset($css['remote']) && $css['remote'] ? NULL : PJ_INSTALL_URL).$css['path'].$css['file'].'" />';
		}
		
		foreach ($controller->getJs() as $js)
		{
			echo '<script src="'.(isset($js['remote']) && $js['remote'] ? NULL : PJ_INSTALL_URL).$js['path'].$js['file'].'"></script>';
		}
		?>
	</head>
	<body>
		<div id="container">
			<div id="header">
				<a href="http://www.phpjabbers.com/knowledge-base-builder/" id="logo" target="_blank"><img src="https://cloud.webbypage.com/index.php/s/wdRNyf7oqYGaa4q/download" alt="" /></a>
			</div>
			<div id="middle">
				<div id="login-content">
				<?php require $content_tpl; ?>
				</div>
			</div> <!-- middle -->
		</div> <!-- container -->
		<div id="footer-wrap">
			<div id="footer">
			   	<p><a href="http://www.webbypage.com/" target="_blank">Apps Team Projects</a> Copyright &copy; <?php echo date("Y"); ?> <a href="http://www.webbypage.com" target="_blank">WebbyPage</a></p>
	        </div>
        </div>
	</body>
</html>