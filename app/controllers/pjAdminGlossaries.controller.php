<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminGlossaries extends pjAdmin
{
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminGlossaries.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionGetGlossary()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjGlossaryModel = pjGlossaryModel::factory()
								->join('pjMultiLang', "t2.model='pjGlossary' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='word'", 'left outer')
								->join('pjMultiLang', "t3.model='pjGlossary' AND t3.foreign_id=t1.id AND t3.locale='".$this->getLocaleId()."' AND t3.field='description'", 'left outer');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjGlossaryModel->where('t2.content LIKE', "%$q%");
				$pjGlossaryModel->orWhere('t3.content LIKE', "%$q%");
			}

			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjGlossaryModel->where('t1.status', $_GET['status']);
			}
				
			$column = 'created';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjGlossaryModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = array();
			$data = $pjGlossaryModel->select("t1.*, t2.content as word")
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
			if (isset($_POST['glossary_create']))
			{
				$pjMultiLangModel = pjMultiLangModel::factory();
				
				$glossary_id = pjGlossaryModel::factory()->setAttributes($_POST)->insert()->getInsertId();
				if ($glossary_id !== false && (int) $glossary_id > 0)
				{
					if (isset($_POST['i18n']))
					{
						$pjMultiLangModel->saveMultiLang($_POST['i18n'], $glossary_id, 'pjGlossary', 'data');
					}
					
					$err = 'AGL03';
				} else {
					$err = 'AGL04';
				}
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminGlossaries&action=pjActionIndex&id=" . $id . "&err=$err");
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
				
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				
				$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminGlossaries.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteGlossary()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			
			if (pjGlossaryModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjMultiLangModel::factory()->where('model', 'pjGlossary')->where('foreign_id', $_GET['id'])->eraseAll();

				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteGlossaryBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjGlossaryModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjGlossary')->whereIn('foreign_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionExportGlossary()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjGlossaryModel::factory()->select("t1.*, t2.content as word, t3.content as description, t2.locale")
						->join('pjMultiLang', "t2.model='pjGlossary' AND t2.foreign_id=t1.id AND t2.field='word'", 'left outer')
						->join('pjMultiLang', "t3.model='pjGlossary' AND t3.foreign_id=t1.id AND t3.locale = t2.locale AND t3.field='description'", 'left outer')
						->whereIn('t1.id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Glossaries-".time().".csv")
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
			$pjGlossaryModel = pjGlossaryModel::factory();
			
			$arr = $pjGlossaryModel->find($_POST['id'])->getData();
			
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
				$pjGlossaryModel->reset()->setAttributes(array('id' => $_POST['id']))->modify(array('is_active' => $sql_status));
			}
		}
		exit;
	}
	
	public function pjActionSaveGlossary()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$pjGlossaryModel = pjGlossaryModel::factory();
			if (!in_array($_POST['column'], $pjGlossaryModel->getI18n()))
			{
				$pjGlossaryModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjGlossary');
			}
		}
		exit;
	}
	
	public function pjActionStatusGlossary()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjGlossaryModel::factory()->whereIn('id', $_POST['record'])->modifyAll(array(
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
			$pjGlossaryModel = pjGlossaryModel::factory();
			$pjMultiLangModel = pjMultiLangModel::factory();
			
			if (isset($_POST['glossary_update']))
			{
				$arr = $pjGlossaryModel->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminGlossaries&action=pjActionIndex&err=AGL08");
				}
				
				$pjGlossaryModel->reset()->set('id', $_POST['id'])->modify($_POST);
				
				$pjMultiLangModel->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjGlossary', 'data');
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminGlossaries&action=pjActionIndex&err=AGL01");
			} else {
				$arr = $pjGlossaryModel->find($_GET['id'])->getData();
				
				if (count($arr) === 0)
				{
					pjUtil::redirect(sprintf("%s?controller=pjAdminGlossaries&action=pjActionIndex&err=%s", $_SERVER['PHP_SELF'], 'AGL08'));
				}
				$pjMultiLangModel = pjMultiLangModel::factory();
				$arr['i18n'] = $pjMultiLangModel->getMultiLang($arr['id'], 'pjGlossary');
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
				
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				
				$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminGlossaries.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>