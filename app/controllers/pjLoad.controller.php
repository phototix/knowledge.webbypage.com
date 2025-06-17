<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjLoad extends pjFront
{
	private $isoDatePattern = '/\d{4}-\d{2}-\d{2}/';
		
	public function pjActionIndex()
	{
		$pjQuestionModel = pjQuestionModel::factory()
								->join('pjMultiLang', "t2.model='pjQuestion' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='question'", 'left outer')
								->join('pjMultiLang', "t3.model='pjQuestion' AND t3.foreign_id=t1.id AND t3.locale='".$this->getLocaleId()."' AND t3.field='answer'", 'left outer')
								->join('pjUser', "t4.id=t1.user_id", 'left outer');
								
		$pjQuestionModel->where('t1.status', "T");
										
		$category_where = "(t1.id IN(SELECT TQC.question_id FROM `".pjQuestionCategoryModel::factory()->getTable()."` as TQC WHERE TQC.category_id IN(SELECT TC.id FROM `".pjCategoryModel::factory()->getTable()."` AS TC WHERE TC.status = 'T')))";	
		if (isset($_GET['keyword']) && !empty($_GET['keyword']))
		{
			$keyword = pjObject::escapeString($_GET['keyword']);
			$pjQuestionModel->where('t2.content LIKE', "%$keyword%");
			$pjQuestionModel->orWhere('t3.content LIKE', "%$keyword%");
		}
		if (isset($_GET['category_id']) && !empty($_GET['category_id']))
		{
			$category_where = "(t1.id IN(SELECT TQC.question_id 
										 FROM `".pjQuestionCategoryModel::factory()->getTable()."` AS TQC 
										 WHERE TQC.category_id IN(SELECT TC.id 
										                          FROM `".pjCategoryModel::factory()->getTable()."` AS TC 
										                          WHERE TC.status = 'T' AND TC.id = ".$_GET['category_id'].")))";
		}
		$pjQuestionModel->where($category_where);
		
		$column = 'created';
		$direction = 'DESC';
		if(!isset($_GET['sortby'])){
			
			$column = 'created';
			$direction = 'DESC';
		}else{
			if($_GET['sortby'] == 'views' || $_GET['sortby'] == 'created'){
				$column = $_GET['sortby'];
				$direction = 'DESC';
			}else if($_GET['sortby'] == 'featured'){
				$pjQuestionModel->where('t1.featured', "T");
				$column = 'created';
				$direction = 'DESC';
			}
		}
		$items_per_page = 10;
		if(isset($this->option_arr['o_items_per_page']))
		{
			$items_per_page = $this->option_arr['o_items_per_page'];
		}
		$total = $pjQuestionModel->findCount()->getData();
		$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : $items_per_page;
		$pages = ceil($total / $rowCount);
		$page = isset($_GET['pjPage']) && (int) $_GET['pjPage'] > 0 ? intval($_GET['pjPage']) : 1;
		$offset = ((int) $page - 1) * $rowCount;
		if ($page > $pages){
			$page = $pages;
		}
		
		$vote_table = pjVoteModel::factory()->getTable();
		$arr = $pjQuestionModel->select("t1.*, t2.content as question, t4.name,
										(SELECT GROUP_CONCAT(`category_id`) FROM `".pjQuestionCategoryModel::factory()->getTable()."` WHERE `question_id` = `t1`.`id` LIMIT 1) AS `category_ids`,
										(SELECT COUNT(TV1.id) FROM `$vote_table` AS TV1 WHERE `TV1`.`question_id` = `t1`.`id` LIMIT 1) AS `cnt`,
										(SELECT SUM(TV2.vote_rate)/COUNT(TV2.id) FROM `$vote_table` AS TV2 WHERE `TV2`.`question_id` = `t1`.`id` LIMIT 1) AS `avg_rate`")
								->orderBy("$column $direction")
								->limit($rowCount, $offset)
								->findAll()
								->toArray('category_ids', ',')
								->getData();
								
		$this->set('arr', $arr);
		$this->set('paginator', array('pages' => $pages, 'total' => $total));
		$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
	}
	
	public function pjActionGlossary()
	{
		$pjGlossaryModel = pjGlossaryModel::factory()
								->join('pjMultiLang', "t2.model='pjGlossary' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='word'", 'left outer')
								->join('pjMultiLang', "t3.model='pjGlossary' AND t3.foreign_id=t1.id AND t3.locale='".$this->getLocaleId()."' AND t3.field='description'", 'left outer');
			
		if (isset($_GET['letter']) && !empty($_GET['letter']))
		{
			$letter = pjObject::escapeString($_GET['letter']);
			$pjGlossaryModel->where('t2.content LIKE', "$letter%");
		}
		$pjGlossaryModel->where('t1.status', 'T');
		
		$arr = $pjGlossaryModel->select("t1.*, t2.content as word,  t3.content as description")
									->orderBy("word ASC")->findAll()->getData();
		$this->set('arr', $arr);
	}
	
	public function pjActionCategory()
	{
		$pjCategoryModel = pjCategoryModel::factory();
		
		$this->set('category_arr', $pjCategoryModel->getNode($this->getLocaleId(), 1));
		
		$_arr = $pjCategoryModel->reset()
	    		->select('t1.*, t2.content AS `name`, t3.content AS `description`,
	    					(SELECT COUNT(TQC.question_id) FROM `'.pjQuestionCategoryModel::factory()->getTable().'` AS TQC WHERE TQC.category_id = t1.id) as cnt_questions')
	    		->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCategory' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left outer')
	    		->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjCategory' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left outer')
	    		->findAll()
	    		->getData();
	    	
	   	$arr = array();
	    foreach($_arr as $v){
	    	$arr[$v['id']]['cnt_questions'] = $v['cnt_questions'];
	    	$arr[$v['id']]['description'] = $v['description'];
	    }
	   
	    $this->set('arr', $arr);
	}
	
	public function pjActionView()
	{
		$pjQuestionModel = pjQuestionModel::factory()
								->join('pjMultiLang', "t2.model='pjQuestion' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='question'", 'left outer')
								->join('pjMultiLang', "t3.model='pjQuestion' AND t3.foreign_id=t1.id AND t3.locale='".$this->getLocaleId()."' AND t3.field='answer'", 'left outer')
								->join('pjUser', "t4.id=t1.user_id", 'left outer');
		
		$vote_table = pjVoteModel::factory()->getTable();
		$arr = $pjQuestionModel->select("t1.*, t2.content as question, t3.content as answer, t4.name,
										(SELECT GROUP_CONCAT(`category_id`) FROM `".pjQuestionCategoryModel::factory()->getTable()."` WHERE `question_id` = `t1`.`id` LIMIT 1) AS `category_ids`,
										(SELECT COUNT(TV1.id) FROM `$vote_table` AS TV1 WHERE `TV1`.`question_id` = `t1`.`id` LIMIT 1) AS `cnt`,
										(SELECT SUM(TV2.vote_rate)/COUNT(TV2.id) FROM `$vote_table` AS TV2 WHERE `TV2`.`question_id` = `t1`.`id` LIMIT 1) AS `avg_rate`")
								->find($_GET['id'])
								->toArray('category_ids', ',')
								->getData();

		if(!empty($arr))
		{
			$pjQuestionModel->reset()->setAttributes(array('id' => $arr['id']))->modify(array('views' => $arr['views'] + 1));
									
			$this->set('arr', $arr);						
		}
		$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
	}
	
	function pjActionSharing()
	{
		$this->setAjax(true);
		
		$pjQuestionModel = pjQuestionModel::factory()
								->join('pjMultiLang', "t2.model='pjQuestion' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='question'", 'left outer')
								->join('pjMultiLang', "t3.model='pjQuestion' AND t3.foreign_id=t1.id AND t3.locale='".$this->getLocaleId()."' AND t3.field='answer'", 'left outer')
								->join('pjUser', "t4.id=t1.user_id", 'left outer');
		
		$vote_table = pjVoteModel::factory()->getTable();
		$arr = $pjQuestionModel->select("t1.*, t2.content as question, t3.content as answer, t4.name,
										(SELECT GROUP_CONCAT(`category_id`) FROM `".pjQuestionCategoryModel::factory()->getTable()."` WHERE `question_id` = `t1`.`id` LIMIT 1) AS `category_ids`,
										(SELECT COUNT(TV1.id) FROM `$vote_table` AS TV1 WHERE `TV1`.`question_id` = `t1`.`id` LIMIT 1) AS `cnt`,
										(SELECT SUM(TV2.vote_rate)/COUNT(TV2.id) FROM `$vote_table` AS TV2 WHERE `TV2`.`question_id` = `t1`.`id` LIMIT 1) AS `avg_rate`")
								->find($_GET['id'])
								->toArray('category_ids', ',')
								->getData();						
		$this->set('arr', $arr);
	}
	
	function pjActionSendSharing()
	{
		$this->setAjax(true);
		
		$pjEmail = new pjEmail();
		
		$from = $_POST['from'];
		$to = $_POST['to'];
		$subject = $_POST['subject'];
		$message = $_POST['message'];
		
		if ($this->option_arr['o_send_email'] == 'smtp')
		{
			$pjEmail
				->setTransport('smtp')
				->setSmtpHost($this->option_arr['o_smtp_host'])
				->setSmtpPort($this->option_arr['o_smtp_port'])
				->setSmtpUser($this->option_arr['o_smtp_user'])
				->setSmtpPass($this->option_arr['o_smtp_pass']);
		}
		
		$pjEmail->setFrom($from)
				->setTo($to)
				->setSubject($subject)
				->send($message);
		
		echo 100;
		exit;
	}
	
	public function pjActionRating()
	{
		$this->setAjax(true);
		
		$pjQuestionModel = pjQuestionModel::factory();
		$pjVoteModel = pjVoteModel::factory();
		
		$access_ip = $_SERVER["REMOTE_ADDR"];
		$is_voted = array();
		$json_arr = array();
		
		$question_id = intval($_POST['id']);
		$rate = intval($_POST['rate']);
		$is_voted = false;
		$limit_vote = 'IP';
		
		if($limit_vote == 'IP'){
			$pjVoteModel->where("t1.question_id = $question_id AND t1.ip = '$access_ip' AND (t1.vote_date + INTERVAL 1 DAY) > NOW()");
			if($pjVoteModel->findCount()->getData() > 0){
				$is_voted = true;
			}
		}else if($limit_vote == 'Cookie'){
			if(isset($_COOKIE["STKB_Vote_" + $question_id])){
				$is_voted = true;
			}
		}
		if ($is_voted) {
			$json_arr['code'] = 101;
		}else{
			$data = array();
			$data['ip'] = $access_ip;
			$data['question_id'] = $question_id;
			$data['vote_rate'] = $rate;
			$pjVoteModel->reset()->setAttributes($data)->insert();
			
			if($limit_vote == 'Cookie'){
				$expire=time() + 60*60*24;
				setcookie("STKB_Vote_" + $question_id, 1, $expire);
			}
			$json_arr['code'] = 102;
			
			$vote_table = $pjVoteModel->getTable();
			$arr = $pjQuestionModel->select("t1.*,
										(SELECT SUM(TV2.vote_rate)/COUNT(TV2.id) FROM `$vote_table` AS TV2 WHERE `TV2`.`question_id` = `t1`.`id` LIMIT 1) AS `avg_rate`")
								->find($_POST['id'])
								->getData();
			$json_arr['avg_rate'] = number_format($arr['avg_rate'], 2);
		}
		pjAppController::jsonResponse($json_arr);
	}
}
?>