<?php
class Main 
{
	var $RP;
	var $DB;

	function __construct() {
		require_once CLASS_DIR.'rp.class.php';
		require_once CLASS_DIR.'pearpdo.class.php';

		$RP = new RP('ua','rewrite','p');
		$DB = new PearPDO();

		$module = $RP->get(0,'client');
		$RP->set('module', $module);

		$RP->setTemplate('admin');

		$RP->set('main_domain', MAIN_DOMAIN);
		$RP->set('admin_url', ADMIN_URL);

		//module分岐
		include MODULE_DIR.'client.module.php';

	}

}