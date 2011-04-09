<?php 
require_once('../public/config.php');
require_once('../library/odtphp/odf.php');

if(isset($_GET['nb']))
	$nb = $_GET['nb'];
else
	$nb = 1;

try {
	$application->bootstrap();
}catch (Zend_Exception $e) {
	echo "Récupère exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
}

$pathModel = ROOT_PATH."/data/models/capture.odt";
$odf = new odf($pathModel);

$moteur = new Gen_Moteur();
$arrDicos = array(
	"concepts"=>"28,34"
	,"syntagmes"=>4
	,"pronoms_complement"=>13
	,"conjugaisons"=>25
	,"pronoms"=>"13,14"
	,"déterminants"=>15
	,"negations"=>16);
$moteur->arrDicos = $arrDicos;	
$moteur->finLigne = "\n";

//récupère la définition des chansons
$dbConcepts = new Model_DbTable_Concepts();
$Rowset = $dbConcepts->find(46990);
$parent = $Rowset->current();
//ajout des généreteurs
$defChansons = $parent->findManyToManyRowset('Model_DbTable_Generateurs','Model_DbTable_ConceptsGenerateurs');
$arrChansons = $defChansons->toArray();

$chansons = $odf->setSegment('chansons');
for ($i = 0; $i < $nb; $i++) {
	$chanson = $arrChansons[mt_rand(0, count($arrChansons)-1)];
	$chansons->setVars('chansons_titre', getipv6());
	//calcul une chanson
	$moteur->Generation($chanson['valeur']);	
	$chansons->setVars('chansons_texte', $moteur->texte);
	//$texte = "";
	//$chansons->setVars('chansons_texte', $texte);
	$chansons->merge();
}
$odf->mergeSegment($chansons);
$odf->exportAsAttachedFile();

function getipv6(){
	$listAlpha = 'abcde0123456789012345678901234567890';
	return $listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.":"
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.":"
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.":"
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.":"
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.":"
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.":"
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.":"
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		.$listAlpha[mt_rand(0, 35)]
		;
	//2001:0db8:0000:85a3:0000:0000:ac1f:8001
}

?>	