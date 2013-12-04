<?php
require_once( "../application/configs/config.php" );
try {
	
	$application->bootstrap();
	

	/*
	$_GET['oeu']=32;
	$_GET['cpt']=158586;
	*/

	if(isset($_GET['test'])){
		$test = true;
		$_GET['frt'] = "html";
	}else
		$test = false;
	
	if(isset($_GET['frt']))
		$frt = $_GET['frt'];
	else
		$frt = "txt";

	if(isset($_GET['audio'])){
		$audio=$_GET['audio'];
		$frt = "html";
	}
	
	if($frt == "html" || $frt == "frg" )
		$rtn = "<br/>";
	else
		$rtn = "\n";
	
	//récupération des paramètres
	if(isset($_GET['oeu']))
		$idOeu = $_GET['oeu'];
	else
		$err = "ERREUR : aucune oeuvre n'est définie.".$rtn;
	
	if(isset($_GET['cpt']))
		$idCpt = $_GET['cpt'];
	else
		$err .= "ERREUR : aucun texte génératif n'est défini.".$rtn;
	if(isset($_GET['btn']))
		$btn = $_GET['btn'];
	else
		$btn = false;
	if(isset($_GET['nb']))
		$nb = $_GET['nb'];
	else
		$nb = 1;
			
		
	if(!$err){
		//récupère le texte génératif
		$dbCpt = new Model_DbTable_Gen_concepts();
		$arrCpt = $dbCpt->findById_concept($idCpt);
		$txtGen = $dbCpt->getGenTexte($arrCpt[0]);
		
		//initialisation du moteur
		$m = new Gen_Moteur();
		
		//récupère les dictionnaires
		$m->arrDicos = $m->getDicosOeuvre($idOeu);
		$m->showErr = $test;
		$m->forceCalcul = false;
		$txts = "";
		for ($i = 0; $i < $nb; $i++) {
			$txt = $m->Generation($txtGen).$rtn;
			if($rtn == "\n")$txt = str_replace("<br/>", "\n", $txt);
			$txts .= $txt;
			if($test)$txts .= "<br>".$m->detail;
		}
		
	}
	//pour l'exécution du script dans une page extérieur au domaine
	header('Access-Control-Allow-Origin: *');
	header("X-XSS-Protection: 0");	
	
	//vérifie le content type à retourner
	if($frt == "html" || $frt == "frg" )
		header('Content-Type: text/html; charset=UTF-8');
	else
		header('Content-Type: text/plain; charset=UTF-8');
	
	//construction de l'audio
	if($audio){
		$today = getdate();
		// Save the MP3 file in this folder with the .mp3 extension 
		$file = $idOeu."_".$idCpt."_".$today[0].".mp3";
		// If the MP3 file exists, do not create a new request
		$audioPath = ROOT_PATH."/data/audio/".$file;
		echo $audioPath."<br/>"; 
		if (!file_exists($audioPath)) {
	    	$mp3 = file_get_contents('http://translate.google.com/translate_tts?ie=UTF-8&q='.$m->texte.'&tl=fr&textlen='.strlen($m->texte).'&idx=0&total=1');
			file_put_contents($audioPath, $mp3);
		}
		$audioPath = WEB_ROOT."/data/audio/".$file;
		$audio='<video controls="true" autoplay="true" name="media"><source src="'.$audioPath.'" type="audio/mpeg"></video>';		
	}
		
		
	//construction du code pour le bouton
	$script = "";
	$scodeBtn = "";
	$codeDiv = "";
	if($btn){
		//construction de l'url de génération
		$params = str_replace("frt=iframe", "frt=frg", $_SERVER['QUERY_STRING']);
		$params = str_replace("frt=html", "frt=frg", $params);
		$urlGen = WEB_ROOT.'/services/api.php';  
		
		$script ='
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js"></script>';
		$codeBtn = '<button type="button" onclick="$.get(\''.$urlGen.'\', {\'oeu\':'.$idOeu.', \'cpt\':'.$idCpt.', \'frt\':\'frg\'}, function(fragment){$(\'#gen'.$idOeu.'_'.$idCpt.'\').html(fragment);});">Génère</button>';		
		$codeDiv = "<div id='gen".$idOeu."_".$idCpt."' >".$txts."</div>".$codeBtn.$audio;		
	}else{
		$codeDiv = $txts.$audio;
	}
	
	
	if($frt == "frg"){
		echo "<div>".$err.$rtn.$txts.$audio."</div>";
	}
	
	if($frt == "html"){
		echo "<html>
	<head>	
		<meta http-equiv='Content-Type' content='text/html;charset=UTF-8'>
		".$script."
	</head>
	<body>
	".$err.$rtn.$codeDiv."
	</body>
</html>";
	}

	if($frt == "txt"){
		echo $err;
		echo $txts;		
	}

	if($frt == "iframe"){
		//construction de l'url de génération
		$params = str_replace("frt=iframe", "frt=html", $_SERVER['QUERY_STRING']);
		$urlGen = WEB_ROOT.'/services/api.php?'.$params;  
		echo '<iframe src="'.$urlGen.'" width="760" height="500" frameborder="0" marginheight="0" marginwidth="0">Chargement en cours...</iframe>';		
	}
	
	
}catch (Zend_Exception $e) {
	echo "Récupère exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
}
?>   		
