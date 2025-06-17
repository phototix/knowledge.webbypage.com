<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjQuestionModel extends pjAppModel
{
	protected $primaryKey = 'id';

	protected $table = 'questions';

	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'user_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'featured', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'views', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
		array('name' => 'modified', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T')
	);
	
	protected $i18n = array('question', 'answer');
	
	public static function factory($attr=array())
	{
		return new self($attr);
	}
}
?>