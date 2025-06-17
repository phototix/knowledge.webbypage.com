<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjGlossaryModel extends pjAppModel
{
	protected $primaryKey = 'id';

	protected $table = 'glossaries';

	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T')
	);
	
	protected $i18n = array('word', 'description');
	
	public static function factory($attr=array())
	{
		return new self($attr);
	}
}
?>