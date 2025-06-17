<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFront extends pjAppController
{
	public $defaultCaptcha = 'PHPJabbersCaptcha';
	
	public $defaultLocale = 'front_locale_id';
	
	public function __construct()
	{
		$this->setLayout('pjActionFront');
	}

	public function afterFilter()
	{		
		$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
			->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
			->where('t2.file IS NOT NULL')
			->orderBy('t1.sort ASC')->findAll()->getData();
		
		$this->set('locale_arr', $locale_arr);
		
		$dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
		$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
		
		$this->appendCss('front_lib.css');
		$this->appendCss('jquery-ui.custom.min.css', PJ_THIRD_PARTY_PATH . 'pj_jquery_ui/css/smoothness/');
		$this->appendCss('overlay.css', PJ_THIRD_PARTY_PATH . 'overlay/themes/light/');
		
		switch ($this->option_arr['o_layout'])
		{
			case 'layout_1':
				$this->appendCss('front_layout_1.css');
				break;
			case 'layout_2':
				$this->appendCss('front_layout_2.css');
				break;
			default:
				$this->appendCss('front_layout_1.css');
				break;
		}
	}
	
	public function beforeFilter()
	{
		$OptionModel = pjOptionModel::factory();
		$this->option_arr = $OptionModel->getPairs($this->getForeignId());
		$this->set('option_arr', $this->option_arr);
		$this->setTime();

		if (!isset($_SESSION[$this->defaultLocale]))
		{
			$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
			if (count($locale_arr) === 1)
			{
				$this->setLocaleId($locale_arr[0]['id']);
			}
		}
		$this->loadSetFields();
	}
	
	public function beforeRender()
	{
		if (isset($_GET['iframe']))
		{
			$this->setLayout('pjActionIframe');
		}
	}
	
	public function pjActionSetLocale()
	{
		$this->setLocaleId(@$_GET['locale']);
		$this->loadSetFields(true);
		pjUtil::redirect($_SERVER['HTTP_REFERER']);
	}
}
?>