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
	//$dbU = new Model_DbTable_flux_Uti();
	//$arr = $dbU->getAll();
	
	/*
	$dbDico = new Model_DbTable_Gen_dicos();
$gen_dicos = array(
  array('id_dico' => '4','nom' => 'gen franÃ§ais syntagmes'),
  array('id_dico' => '13','nom' => 'gen franÃ§ais pronom complÃ©ment'),
  array('id_dico' => '14','nom' => 'gen franÃ§ais pronom sujet'),
  array('id_dico' => '34','nom' => 'gen franÃ§ais gÃ©nÃ©ral'),
  array('id_dico' => '44','nom' => 'gen franÃ§ais conjugaison'),
  array('id_dico' => '45','nom' => 'gen ?'),
  array('id_dico' => '46','nom' => 'gen franÃ§ais dÃ©terminants'),
  array('id_dico' => '51','nom' => 'gen ?'),
  array('id_dico' => '72','nom' => 'gen ?'),
  array('id_dico' => '73','nom' => 'gen espagnol gÃ©nÃ©ral'),
  array('id_dico' => '82','nom' => 'capture critiques'),
  array('id_dico' => '67','nom' => 'gen espagnol conjugaisons'),
  array('id_dico' => '68','nom' => 'gen espagnol syntagme'),
  array('id_dico' => '69','nom' => 'gen espagnol dÃ©terminant'),
  array('id_dico' => '70','nom' => 'gen espagnol pronom complÃ©ment'),
  array('id_dico' => '84','nom' => 'gen ?'),
  array('id_dico' => '89','nom' => 'gen ?'),
  array('id_dico' => '90','nom' => 'gen ?'),
  array('id_dico' => '91','nom' => 'gen ?'),
  array('id_dico' => '92','nom' => 'gen ?'),
  array('id_dico' => '93','nom' => 'capture bios'),
  array('id_dico' => '94','nom' => 'capture chansons'),
  array('id_dico' => '96','nom' => 'capture twitter'),
  array('id_dico' => '98','nom' => 'gen ?'),
  array('id_dico' => '99','nom' => 'thÃ©Ã¢tre'),
  array('id_dico' => '101','nom' => 'vies'),
  array('id_dico' => '102','nom' => 'Proverbes web'),
  array('id_dico' => '104','nom' => 'Dico ?'),
  array('id_dico' => '109','nom' => 'test'),
  array('id_dico' => '110','nom' => 'mon dico'),
  array('id_dico' => '111','nom' => 'monostiches'),
  array('id_dico' => '112','nom' => 'DS_Monostiches'),
  array('id_dico' => '117','nom' => 'mon dico'),
  array('id_dico' => '114','nom' => 'mon dictionnaire'),
  array('id_dico' => '115','nom' => 'mon mono'),
  array('id_dico' => '116','nom' => 'fredv'),
  array('id_dico' => '118','nom' => 'DS_Climats'),
  array('id_dico' => '119','nom' => 'mon dico'),
  array('id_dico' => '120','nom' => 'mon dico'),
  array('id_dico' => '121','nom' => 'DS-Corneille'),
  array('id_dico' => '122','nom' => 'DS-Corneille'),
  array('id_dico' => '123','nom' => 'DS_Tests-Ressources'),
  array('id_dico' => '127','nom' => 'Eric'),
  array('id_dico' => '128','nom' => 'test herbier sam'),
  array('id_dico' => '129','nom' => 'DS-PoÃ©tiques'),
  array('id_dico' => '130','nom' => 'DS-Trajectoires')
  );
	$nbTot = 0;
	foreach ($gen_dicos as $dico){
		echo '<br/>'.$dico['id_dico'].' - '.$dico['nom'].'<br/>';
		$nb = $dbDico->remove($dico['id_dico']);		
		echo $nb.'<br/>';
	}	
	echo $nbTot.'<br/>';
	*/
	
	/*
	$idDico = 118;
	$idConj = 44;
    $oDico = new Gen_Dico();
    $oDico->importCSV($idDico, array("path_source"=>"http://localhost/generateur/data/dicos/5236b986d497f.csv"));
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
	$ac = $dbC->findAllByDicos(array("concepts"=>"102, 34","pronoms"=>"13, 14","syntagmes"=>"4","déterminants"=>"46","negations"=>"16"));
	$ac = $dbC->findByIdDico("102.34",true);
	print_r($ac);
	$dbC->ajouter(array("id_dico"=>102,"lib"=>"bidule","type"=>"dis"));	
	$dbC->utilise(157625, "a_testouill");
	$arr = $dbC->remove(157632);
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
	$dbT = new Model_DbTable_Gen_conjugaisons();
	$arrTrmChange[]=array("id_trm"=>"24712","lib"=>"ous");	
	$dbT->editTerms($arrTrmChange);
	$arr = $dbT->findVerbeByIdConj('824');
	$arr = $dbT->findTermByIdConj(680);
	*/
	
	
	/*
	$arrVerifDico = array("concepts"=>"120, 118, 34"	
		,"conjugaisons"=>"44"	
		,"déterminants"=>"46"	
		,"negations"=>"16"	
		,"pronoms"=>"13, 14, 108"	
		,"pronoms_complement"=>"13"	
		,"pronoms_sujet"=>"14"	
		,"pronoms_sujet_indefini"=>"108"	
		,"syntagmes"=>"4");	
		$arrVerifDico = array("concepts"=>"42, 124"	
	,"conjugaisons"=>"40"	
	,"déterminants"=>"39"	
	,"negations"=>"16"	
	,"négations"=>"16"	
	,"pronoms"=>"38, 126, 108"	
	,"pronoms_complement"=>"38"	
	,"pronoms_sujet"=>"126"	
	,"pronoms_sujet_indefini"=>"108"	
	,"syntagmes"=>"41");	
	*/	

	/*
	$txts = array("Lennon Nicotiana tabacum%%[12|a_poète@carac5] ");	
	$m = new Gen_Moteur();
	//$var = $m->Verifier($txts[0], $arrVerifDico);
	$var = $m->Tester($txts, $arrVerifDico);
	echo $var[0];
	echo "<br>".$m->detail;
	$m->getArbreGen("[010000000|v_sembler 1] [090000000|v_accepter 1] ",$arrVerifDico);
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
	$dbV->ajouter(array("id_dico"=>102,"lib"=>"bidouiller","type"=>"v"),array("elision"=>"0","id_conj"=>"771","id_dico"=>102,"prefix"=>"bidouille"));	
	*/
	
	/*
	$gdata = new Flux_Gdata("projetgapaii@gmail.com","HakNasSam");
	$gdata->saveSpreadsheetsDico();
	*/
	/*
	$dbGen = new Model_DbTable_Gen_generateurs();
	$dbGen->remove(192667, 157623);
	*/
	
	/*
	$dbA = new Model_DbTable_Gen_adjectifs();
	$dbA->editMulti(array(array("id_adj"=>"19887"
		,"val"=>array("f_p"=>"es", "f_s"=>"e", "m_p"=>"s", "prefix"=>"cramoisi")
		)));
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