<?php 

$local_domain = (!empty($_SERVER['SERVER_NAME']) && strpos($_SERVER['SERVER_NAME'], ".localhost")!==FALSE) ? ".localhost" : '';

if(empty($local_domain)) {
	define("MAIN_DOMAIN", "m-con.net");
	define("ADMIN_DOMAIN", "admin.m-con.net");
} else {
	//ローカルデバッグ環境用
	define("MAIN_DOMAIN", "m-con.localhost.net");
	define("ADMIN_DOMAIN", "m-con.localhost.net:8088");
}
define("BASE_URL" , "https://".MAIN_DOMAIN."/");
define("ADMIN_URL", "https://".ADMIN_DOMAIN."/");

define('DB_HOST',    (empty($_ENV['MYSQL_HOST']) ? '': $_ENV['MYSQL_HOST']) );
define('DB_NAME',    (empty($_ENV['MYSQL_DATABASE']) ? '': $_ENV['MYSQL_DATABASE']));
define('DB_USER',    (empty($_ENV['MYSQL_USER']) ? '': $_ENV['MYSQL_USER']));
define('DB_PASSWORD',(empty($_ENV['MYSQL_PASSWORD']) ? '': $_ENV['MYSQL_PASSWORD']));

define("ROOT_DIR"   , "/var/www/admin/");
define("CLASS_DIR"  , ROOT_DIR . "class/");
define("MODULE_DIR" , ROOT_DIR . "module/");
define("TPL_DIR"    , ROOT_DIR . "templates/");
define("TPL_DIR_C"  , ROOT_DIR . "templates_c/");

// Smarty expects SMARTY_DIR to point at the libs/ directory
define('SMARTY_DIR' , ROOT_DIR.'lib/smarty-4.3.1/libs/');
require_once SMARTY_DIR . 'Smarty.class.php';