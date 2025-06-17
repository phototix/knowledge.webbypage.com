<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjQuestionCategoryModel extends pjAppModel
{
	protected $primaryKey = null;
	
	protected $table = 'questions_categories';
	
	protected $schema = array(
		array('name' => 'question_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'category_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new self($attr);
	}
}
?>