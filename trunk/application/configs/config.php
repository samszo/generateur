<?php
date_default_timezone_set('Europe/Paris');
ini_set("memory_limit",'1600M');

define ("WEB_ROOT","http://localhost/generateur");
define ("ROOT_PATH","c:\wamp\www\generateur");
define ("WEB_ROOT_AJAX",WEB_ROOT."/public");


// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));


set_include_path(APPLICATION_PATH.'/../library');       
set_include_path(get_include_path().PATH_SEPARATOR."C:\wamp\www\ZendFramework-1.12.0\library");
set_include_path(get_include_path().PATH_SEPARATOR."C:\wamp\www\ZendFramework-1.12.0\extras\library");

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

// Désactivez le plugin ErrorHandler :
//$front = Zend_Controller_Front::getInstance();
//$front->throwExceptions(true);
// Désactivez le plugin ErrorHandler :
//$front->setParam('noErrorHandler', true);        
?>