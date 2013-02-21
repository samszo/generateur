<?php
require_once( "../application/configs/config.php" );

try {
	
	$application->bootstrap();
	
	$server = new Zend_Amf_Server();

	$server->addDirectory(APPLICATION_PATH);
	$server->addDirectory(ROOT_PATH.'/library/php/');

	//$server->setClass("Models_DbTable_OeuvresDicos");

	$response = $server->handle();

}catch (Zend_Exception $e) {
	echo "Récupère exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
}
   		
echo $response;
