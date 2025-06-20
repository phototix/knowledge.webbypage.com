<?php
if (!headers_sent())
{
	session_name('KnowledgebaseBuilder');
	@session_start();

	$tenants = ['webby', 'kms', 'icdi', 'general'];

	// Handle dropdown form submission
	if (isset($_POST['tenant']) && in_array($_POST['tenant'], $tenants)) {
		$_SESSION['tenant_prefix'] = $_POST['tenant'] . '_';
		header("Location: ".$_SERVER['PHP_SELF']);
		exit;
	}

	// Set default if not selected
	if (!isset($_SESSION['tenant_prefix'])) {
		$_SESSION['tenant_prefix'] = 'webby_';
	}

}
if (isset($_GET["reporting"]) && $_GET["reporting"] == '0') 
{
	$_SESSION["error_reporting"] = '0';
} else if (isset($_GET["reporting"]) && $_GET["reporting"]== '1') {
	$_SESSION["error_reporting"] = '1';
}
if (isset($_SESSION["error_reporting"]) && $_SESSION["error_reporting"]=='1')
{
	ini_set("display_errors", "On");
	error_reporting(E_ALL|E_STRICT);
} else {
	error_reporting(0);
}
header("Content-type: text/html; charset=utf-8");
if (!defined("ROOT_PATH"))
{
	define("ROOT_PATH", dirname(__FILE__) . '/');
}
require ROOT_PATH . 'app/config/options.inc.php';
require_once PJ_FRAMEWORK_PATH . 'pjAutoloader.class.php';
pjAutoloader::register();
if (!isset($_GET['controller']) || empty($_GET['controller']))
{
	header("HTTP/1.1 301 Moved Permanently");
	pjUtil::redirect(PJ_INSTALL_URL . basename($_SERVER['PHP_SELF'])."?controller=pjAdmin&action=pjActionIndex");
}

if (isset($_GET['controller']))
{
	$pjObserver = pjObserver::factory();
	$pjObserver->init();
}
?>
<form method="post" style="position:fixed;top:10px;right:10px;z-index:9999;">
    <label for="tenant">Tenant:</label>
    <select name="tenant" id="tenant" onchange="this.form.submit()">
        <?php foreach ($tenants as $tenant): ?>
            <option value="<?= $tenant ?>" <?= $_SESSION['tenant_prefix'] === $tenant . '_' ? 'selected' : '' ?>><?= ucfirst($tenant) ?></option>
        <?php endforeach; ?>
    </select>
</form>