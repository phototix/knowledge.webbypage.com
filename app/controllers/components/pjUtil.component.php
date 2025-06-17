<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjUtil extends pjToolkit
{
	static public function getBreadcrumbTree(&$stack, $category_arr, $id)
	{
		foreach ($category_arr as $category)
		{
			if ($category['data']['id'] == $id)
			{
				if ($category['deep'] == 0)
				{
					$stack[] = $category;
				} else {
					$stack[] = $category;
					pjUtil::getBreadcrumbTree($stack, $category_arr, $category['data']['parent_id']);
				}
				
				break;
			}
		}
	}
	
	static public function getCurrentURL() {
 		$pageURL = 'http';
 		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") 
 		{
 			$pageURL .= "s";
 		}
 		$pageURL .= "://";
 		if ($_SERVER["SERVER_PORT"] != "80") 
 		{
  			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 		} else {
  			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 		}
 		return $pageURL;
	}
}
?>