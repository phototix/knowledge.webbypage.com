<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminQuestions extends pjAdmin
{
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
			$this->set('user_arr', pjUserModel::factory()->orderBy("name ASC")->findAll()->getData());

			$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
			$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminQuestions.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionGetQuestion()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjQuestionModel = pjQuestionModel::factory()
								->join('pjMultiLang', "t2.model='pjQuestion' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='question'", 'left outer')
								->join('pjMultiLang', "t3.model='pjQuestion' AND t3.foreign_id=t1.id AND t3.locale='".$this->getLocaleId()."' AND t3.field='answer'", 'left outer')
								->join('pjUser', "t4.id=t1.user_id", 'left outer');
			if($this->isEditor()){
				$pjQuestionModel->where('t1.user_id', $this->getUserId());
			}
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjQuestionModel->where('t2.content LIKE', "%$q%");
				$pjQuestionModel->orWhere('t3.content LIKE', "%$q%");
			}
			if (isset($_GET['question']) && !empty($_GET['question']))
			{
				$q = pjObject::escapeString($_GET['question']);
				$pjQuestionModel->where('t2.content LIKE', "%$q%");
			}
			if (isset($_GET['answer']) && !empty($_GET['answer']))
			{
				$q = pjObject::escapeString($_GET['answer']);
				$pjQuestionModel->where('t3.content LIKE', "%$q%");
			}
			if (isset($_GET['user_id']) && !empty($_GET['user_id']))
			{
				$user_id = pjObject::escapeString($_GET['user_id']);
				$pjQuestionModel->where('t1.user_id', $user_id);
			}
			if (isset($_GET['category_id']) && !empty($_GET['category_id']))
			{
				$category_id = pjObject::escapeString($_GET['category_id']);
				$pjQuestionModel->where("(t1.id IN(SELECT TQC.question_id FROM `".pjQuestionCategoryModel::factory()->getTable()."` as TQC WHERE TQC.category_id = $category_id))");
			}

			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjQuestionModel->where('t1.status', $_GET['status']);
			}
				
			$column = 'created';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjQuestionModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = array();
			
			$data = $pjQuestionModel->select("t1.*, t2.content as question, t4.name")
									->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
				
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['question_create']))
			{
				$pjMultiLangModel = pjMultiLangModel::factory();
				$data = array();
				if($this->isEditor()){
					$data['user_id'] = $this->getUserId();
				}
				$question_id = pjQuestionModel::factory()->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
				if ($question_id !== false && (int) $question_id > 0)
				{
					if (isset($_POST['i18n']))
					{
						$pjMultiLangModel->saveMultiLang($_POST['i18n'], $question_id, 'pjQuestion', 'data');
					}
					
					if (isset($_POST['category_id']) && count($_POST['category_id']) > 0)
					{
						$pjQuestionCategoryModel = pjQuestionCategoryModel::factory();
						$pjQuestionCategoryModel->begin();
						foreach ($_POST['category_id'] as $category_id)
						{
							$pjQuestionCategoryModel
								->reset()
								->set('question_id', $question_id)
								->set('category_id', $category_id)
								->insert();
						}
						$pjQuestionCategoryModel->commit();
					}
					
					$err = 'AQ03';
				} else {
					$err = 'AQ04';
				}
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminQuestions&action=pjActionIndex&id=" . $id . "&err=$err");
			} else {
				
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left outer')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
						
				$lp_arr = array();
				foreach ($locale_arr as $v)
				{
					$lp_arr[$v['id']."_"] = $v['file'];
				}
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
				$this->set('user_arr', pjUserModel::factory()->orderBy("name ASC")->findAll()->getData());
				
				$this->appendJs('jquery.multiselect.min.js', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendCss('jquery.multiselect.css', PJ_THIRD_PARTY_PATH . 'multiselect/');
				
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
				
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminQuestions.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteQuestion()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			
			if (pjQuestionModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjMultiLangModel::factory()->where('model', 'pjQuestion')->where('foreign_id', $_GET['id'])->eraseAll();
				pjQuestionCategoryModel::factory()->where('question_id', $_GET['id'])->eraseAll();
				pjVoteModel::factory()->where('question_id', $_GET['id'])->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteQuestionBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjQuestionModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjQuestion')->whereIn('foreign_id', $_POST['record'])->eraseAll();
				pjQuestionCategoryModel::factory()->whereIn('question_id', $_POST['record'])->eraseAll();
				pjVoteModel::factory()->whereIn('question_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionExportQuestion()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjQuestionModel::factory()->select("t1.*, t2.content as question, t3.content as answer, t2.locale")
						->join('pjMultiLang', "t2.model='pjQuestion' AND t2.foreign_id=t1.id AND t2.field='question'", 'left outer')
						->join('pjMultiLang', "t3.model='pjQuestion' AND t3.foreign_id=t1.id AND t3.locale = t2.locale AND t3.field='answer'", 'left outer')
						->whereIn('t1.id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Questions-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionSetActive()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$pjQuestionModel = pjQuestionModel::factory();
			
			$arr = $pjQuestionModel->find($_POST['id'])->getData();
			
			if (count($arr) > 0)
			{
				switch ($arr['is_active'])
				{
					case 'T':
						$sql_status = 'F';
						break;
					case 'F':
						$sql_status = 'T';
						break;
					default:
						return;
				}
				$pjQuestionModel->reset()->setAttributes(array('id' => $_POST['id']))->modify(array('is_active' => $sql_status));
			}
		}
		exit;
	}
	
	public function pjActionSaveQuestion()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$pjQuestionModel = pjQuestionModel::factory();
			if (!in_array($_POST['column'], $pjQuestionModel->getI18n()))
			{
				$pjQuestionModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjQuestion');
			}
		}
		exit;
	}
	
	public function pjActionStatusQuestion()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjQuestionModel::factory()->whereIn('id', $_POST['record'])->modifyAll(array(
					'status' => ":IF(`status`='F','T','F')"
				));
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$pjQuestionModel = pjQuestionModel::factory();
			$pjQuestionCategoryModel = pjQuestionCategoryModel::factory();
			$pjMultiLangModel = pjMultiLangModel::factory();
			
			if (isset($_POST['question_update']))
			{
				$arr = $pjQuestionModel->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminQuestions&action=pjActionIndex&err=AQ08");
				}
				$data = array();
				$data['modified'] = date('Y-m-d H:i:s');
				$pjQuestionModel->reset()->set('id', $_POST['id'])->modify(array_merge($_POST, $data));
				
				$pjQuestionCategoryModel->where('question_id', $_POST['id'])->eraseAll();
				if (isset($_POST['category_id']) && count($_POST['category_id']) > 0)
				{
					$pjQuestionCategoryModel->begin();
					foreach ($_POST['category_id'] as $category_id)
					{
						$pjQuestionCategoryModel
							->reset()
							->set('question_id', $_POST['id'])
							->set('category_id', $category_id)
							->insert();
					}
					$pjQuestionCategoryModel->commit();
				}
				
				$pjMultiLangModel->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjQuestion', 'data');
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminQuestions&action=pjActionIndex&err=AQ01");
			} else {
				$vote_table = pjVoteModel::factory()->getTable();
				$arr = $pjQuestionModel->select("t1.*,  (SELECT COUNT(TV1.id) FROM `$vote_table` AS TV1 WHERE `TV1`.`question_id` = `t1`.`id` LIMIT 1) AS `cnt`,
														(SELECT SUM(TV2.vote_rate)/COUNT(TV2.id) FROM `$vote_table` AS TV2 WHERE `TV2`.`question_id` = `t1`.`id` LIMIT 1) AS `avg_rate`")
										->find($_GET['id'])->getData();
				
				if (count($arr) === 0)
				{
					pjUtil::redirect(sprintf("%s?controller=pjAdminQuestions&action=pjActionIndex&err=%s", $_SERVER['PHP_SELF'], 'AQ08'));
				}
				$pjMultiLangModel = pjMultiLangModel::factory();
				$arr['i18n'] = $pjMultiLangModel->getMultiLang($arr['id'], 'pjQuestion');
				$this->set('arr', $arr);
				
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left outer')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
						
				$lp_arr = array();
				foreach ($locale_arr as $v)
				{
					$lp_arr[$v['id']."_"] = $v['file'];
				}
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
				$this->set('user_arr', pjUserModel::factory()->orderBy("name ASC")->findAll()->getData());
				$this->set('qc_arr', pjQuestionCategoryModel::factory()->where('t1.question_id', $arr['id'])->orderBy('t1.category_id ASC')->findAll()->getDataPair('category_id', 'category_id'));
				
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
				$this->appendJs('jquery.ui.stars.min.js', PJ_THIRD_PARTY_PATH . 'jstarring/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminQuestions.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionResetVote()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			pjVoteModel::factory()->where('question_id', $_GET['id'])->eraseAll();
			pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminQuestions&action=pjActionUpdate&id=" . $_GET['id']);
		} else {
			$this->set('status', 2);
		}
		exit;
	}
}
?>