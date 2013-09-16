<?php
require_once( "../application/configs/config.php" );
try {
	
	$application->bootstrap();
	

	/*
	$_GET['oeu']=6;
	$_GET['cpt']=157623;
	*/
	
	//récupération des paramètres
	if(isset($_GET['oeu']))
		$idOeu = $_GET['oeu'];
	else
		$err = "ERREUR : aucune oeuvre n'est définie.".$rtn;
	
	if(isset($_GET['cpt']))
		$idCpt = $_GET['cpt'];
	else
		$err .= "ERREUR : aucun texte génératif n'est défini.".$rtn;
	if(isset($_GET['frt']))
		$frt = $_GET['frt'];
	else
		$frt = "txt";
	if(isset($_GET['btn']))
		$btn = $_GET['btn'];
	else
		$btn = false;
	if(isset($_GET['nb']))
		$nb = $_GET['nb'];
	else
		$nb = 1;

	if($frt == "html" || $frt == "frg" )
		$rtn = "<br/>";
	else
		$rtn = "\n";
		
	if(!$err){
		//récupère les dictionnaires
		$dbODU = new Model_DbTable_Gen_oeuvresxdicosxutis();
		$arrDico = $dbODU->findByIdOeu($idOeu);		
		foreach ($arrDico as $dico) {
			if(substr($dico["type"],0,7)=="pronoms" ){
				if($arrVerifDico["pronoms"]){
					$arrVerifDico["pronoms"] .= ", ".$dico["id_dico"];
				}else{
					$arrVerifDico["pronoms"]=$dico["id_dico"];
				}			
			}
			if($dico["type"]=="négations" ){
				if($arrVerifDico["negations"]){
					$arrVerifDico["negations"] .= ", ".$dico["id_dico"];
				}else{
					$arrVerifDico["negations"]=$dico["id_dico"];
				}			
			}
			if($arrVerifDico[$dico["type"]]){
				$arrVerifDico[$dico["type"]] .= ", ".$dico["id_dico"];
			}else{
				$arrVerifDico[$dico["type"]]=$dico["id_dico"];
			}
		}
		//récupère le texte génératif
		$dbCpt = new Model_DbTable_Gen_concepts();
		$arrCpt = $dbCpt->findById_concept($idCpt);
		$txtGen = '['.$arrCpt[0]["type"].'-'.$arrCpt[0]["lib"].']';
		
		//génére le texte
		$m = new Gen_Moteur();
		$m->arrDicos = $arrVerifDico;
		$m->forceCalcul = false;
		
		$txt = "";
		for ($i = 0; $i < $nb; $i++) {
			$txt .= $m->Generation($txtGen).$rtn;			
		}
		
	}
		
	header('Content-Type: text/html; charset=UTF-8');
	
		
	//construction du code pour le bouton
	$script = "";
	$scodeBtn = "";
	$codeDiv = "";
	if($btn){
		//construction de l'url de génération
		$params = str_replace("frt=iframe", "frt=frg", $_SERVER['QUERY_STRING']);
		$params = str_replace("frt=html", "frt=frg", $params);
		$urlGen = WEB_ROOT.'/services/api.php?'.$params;  
		
		$script ='
<script type="text/javascript" src="http://mbostock.github.com/d3/d3.js"></script>
<script type="text/javascript">
	var urlGen = "'.$urlGen.'";
	function load() {
		d3.text(urlGen, function(fragment) {
			d3.select("#generation").html(fragment);
		});
	}
</script>';
		$codeBtn = '<button type="button" onclick="load()">Génère</button>';		
		$codeDiv = "<div id='generation' >".$txt."</div>".$codeBtn;		
	}else{
		$codeDiv = $txt;
	}
	
	if($frt == "frg"){
		echo "<div>".$err.$rtn.$txt."</div>";
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
		echo $txt;		
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
