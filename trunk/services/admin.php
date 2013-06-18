<?php
require_once( "../application/configs/config.php" );

try {
	
	$application->bootstrap();

	
	/*
	$dbC = new Model_DbTable_Gen_concepts();
	$ac = $dbC->findByIdDico("102.34",true);
	$dbC->ajouter(array("id_dico"=>102,"lib"=>"bidule","type"=>"dis"));	
	$dbC->utilise(157625, "a_testouill");
	$arr = $dbC->remove(157632);
	*/
	
	/*
	$dbN = new Model_DbTable_Gen_determinants();
	$arr = $dbN->utilise(46, 46);
	*/
	
	/*
	$dbN = new Model_DbTable_Gen_negations();
	$arr = $dbN->utilise(16, 1);
	*/
	
	/*
	$dbA = new Model_DbTable_flux_acti();
	//$xml = $dbA->getFullPath(9);
	$xml = $dbA->getActiForOeuvre(6);
	$idActi = $dbA->ajoutForUtis("dictionnaire syntagme : modifier : à -> àbb (ref:4_429)","1,2",6,4);
	*/
	
	/*
	$dbD = new Model_DbTable_Gen_determinants();
	$arr = $dbD->findByIdDico(46);
	*/
	/*
	$dbV = new Model_DbTable_Gen_verbes();
	$dbV->ajouter(array("id_dico"=>102,"lib"=>"bidouiller","type"=>"v"),array("elision"=>"0","id_conj"=>"771","id_dico"=>102,"prefix"=>"bidouille"));	
	*/
	/*
	$dbT = new Model_DbTable_Gen_conjugaisons();
	$arrTrmChange[]=array("id_trm"=>"24712","lib"=>"ous");	
	$dbT->editTerms($arrTrmChange);
	$arr = $dbT->findVerbeByIdConj('824');
	$arr = $dbT->findTermByIdConj(680);
	*/
	
	/*
	$arrVerifDico = array("concepts"=>"93,94,82,34,102"	
		,"conjugaisons"=>"44"	
		,"déterminants"=>"46"	
		,"négations"=>"16"	
		,"pronoms_complement"=>"13"	
		,"pronoms_sujet"=>"14"	
		,"syntagmes"=>"4");	
	$txts = array("[dis-NK8yf]");	
	$m = new Gen_Moteur();
	$m->Tester($txts, $arrVerifDico);
	$m->getArbreGen("[0|caract1] [0|caract2], [thl-visage-01], [thl-allure-01]",$arrVerifDico);
	$arr = $m->arrClass;
	*/
	
	/*
	$dbS = new Model_DbTable_Gen_syntagmes();
	$dbS->utiliseCpt(157638, "s_brouc");
	$dbS->ajouter(array("id_dico"=>4,"lib"=>"test"));
	$arr = $dbS->findByIdConcept(56203, "4");
	*/
		
	/* 
	$dbODU = new Model_DbTable_Gen_oeuvresxdicosxutis();
	$dbODU->remove(55);
	$dbODU->findByIdOeu(6);
	*/
	
	/*
	$dbV = new Model_DbTable_Gen_verbes();
	$arr = $dbV->findByIdConcept('60298');

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
	/*
	$dbGen = new Model_DbTable_Gen_generateurs();
	$dbGen->remove(192667, 157623);
	*/
	
	$server = new Zend_Amf_Server();

	$server->addDirectory(APPLICATION_PATH);
	$server->addDirectory(ROOT_PATH.'/library');
		
	//$server->setClass("Gen_Moteur");
	$server->setProduction(false);
	
	$response = $server->handle();

}catch (Zend_Exception $e) {
	echo "Récupère exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
}
   		
echo $response;