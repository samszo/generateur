<?php
require_once( "../application/configs/config.php" );
try {
	
	$application->bootstrap();
	
	/*
	$row = 1;
	if (($handle = fopen("https://docs.google.com/spreadsheet/pub?key=0AsJMmUNA97M2dGxwUVltUWNQazVwMGZ0b3V6enVaS0E&single=true&gid=0&output=csv", "r")) !== FALSE) {
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	        $num = count($data);
	        echo "<p> $num champs à la ligne $row: <br /></p>\n";
	        $row++;
	        for ($c=0; $c < $num; $c++) {
	            echo $data[$c] . "<br />\n";
	        }
	    }
	    fclose($handle);
	}
	$oSite = new Flux_Site();
	$csv = $oSite->getUrlBodyContent("");
	$arr = str_getcsv($csv);
	$toto = "";
	*/
	
	/*
	$idDico = 110;
	$idConj = 44;
    $oDico = new Gen_Dico();
    $oDico->importCSV($idDico, array("path_source"=>"http://localhost/generateur/data/dicos/522577f966afd.csv"));
    $oDico->GetMacToXml($idDico);
    $oDico->SaveBdd($idDico, $idConj);
	*/
	
	/*
	$a = new Auth_LoginManager();
	$user = new Auth_LoginVO();
	$user->username='samszo';
	$user->password='samszo';
	$r = $a->verifyUser($user);
	print_r($r);
	*/	
	/*
	$dbC = new Model_DbTable_Gen_concepts();
	$ac = $dbC->findByIdDico("102.34",true);
	print_r($ac);
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
	$arrVerifDico = array("concepts"=>"111, 112, 34"	
		,"conjugaisons"=>"44"	
		,"déterminants"=>"46"	
		,"négations"=>"16"	
		,"pronoms"=>"13, 14, 108"	
		,"pronoms_complement"=>"13"	
		,"pronoms_sujet"=>"14"	
		,"pronoms_sujet_indefini"=>"108"	
		,"syntagmes"=>"4");	
		
		
	$txts = array("[m_animal 4]");	
	$m = new Gen_Moteur();
	$var = $m->Tester($txts, $arrVerifDico);
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