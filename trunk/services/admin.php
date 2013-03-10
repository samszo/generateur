<?php
require_once( "../application/configs/config.php" );

try {
	
	$application->bootstrap();

	/*
	$a = new Auth_LoginManager();
	$user = new Auth_LoginVO();
	$user->username='samszo';
	$user->password='samszo';
	$r = $a->verifyUser($user);
	*/
	
	/*
	$gdata = new Flux_Gdata("projetgapaii@gmail.com","HakNasSam");
	$gdata->saveSpreadsheetsDico();
	*/
	
	$server = new Zend_Amf_Server();

	$server->addDirectory(APPLICATION_PATH);
	$server->addDirectory(ROOT_PATH.'/library');
		
	//$server->setClass("Model_DbTable_OeuvresDicos");
	$server->setProduction(false);
	
	$response = $server->handle();

}catch (Zend_Exception $e) {
	echo "Récupère exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
}
   		
echo $response;