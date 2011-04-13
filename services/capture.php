<?php
set_time_limit(1000);
ini_set("memory_limit",'1600M');

$temps_debut = microtime(true);


require_once('../public/config.php');
require_once('../library/odtphp/odf.php');

if(isset($_GET['nb']))
	$nb = $_GET['nb'];
else
	$nb = 1;

if(isset($_GET['id']))
	$id = $_GET['id'];
else
	$id = 46990;
	
if(isset($_GET['odf']))
	$getOdf = true;
else
	$getOdf = false;

if(isset($_GET['err']))
	$err = true;
else
	$err = false;

if(isset($_GET['dicos']))
	$dicos = $_GET['dicos'];
else
	$dicos = "28,34,27";
	
	
if(!$getOdf)header ('Content-type: text/html; charset=utf-8');
	
	
try {

	$application->bootstrap();


	//récupère le nombre de fichier
	$nbFic = count_files(ROOT_PATH.'/data/capture/','txt','');
	
	//vérifie si on fait un fichier
	if($getOdf){
		$pathModel = ROOT_PATH."/data/models/capture.odt";
		$odf = new odf($pathModel);
	}
	
	$arrDicos = array(
		"concepts"=>$dicos
		,"syntagmes"=>4
		,"pronoms_complement"=>13
		,"conjugaisons"=>25
		,"pronoms"=>"13,14"
		,"déterminants"=>15
		,"negations"=>16);
	
	//récupère la définition des chansons
	$dbConcepts = new Model_DbTable_Concepts();
	$Rowset = $dbConcepts->find($id);
	$parent = $Rowset->current();
	//ajout des généreteurs
	$defChansons = $parent->findManyToManyRowset('Model_DbTable_Generateurs','Model_DbTable_ConceptsGenerateurs');
	$arrChansons = $defChansons->toArray();
	
	//nimbre de chansons possible
	$nbChan = count($arrChansons)-1;
	
	if($getOdf)$chansons = $odf->setSegment('chansons');
	for ($itr = 0; $itr < $nb; $itr++) {
		try {
			$num = mt_rand(0, $nbChan);
			$chanson = $arrChansons[$num];
			
			if(!$getOdf){
				echo "<br/>".$itr." sur ".$nb;
				echo " texte ".$num." sur ".$nbChan;
			}
			
			if($getOdf)$chansons->setVars('chansons_titre', getipv6());

			//calcul une chanson
			$moteur = new Gen_Moteur();
			$moteur->arrDicos = $arrDicos;	
			//$moteur->finLigne = "\r\n";
			$moteur->showErr = $err;
			if(!$getOdf)echo "<br/>".$chanson['valeur']."<br/>";

			$moteur->Generation($chanson['valeur']);	
			
			if(!$getOdf)echo "<br/>".$moteur->texte."<br/>";
			
			if($getOdf){
				//ajoute le texte au doc
				$chansons->setVars('chansons_texte', str_replace("<br/>","\n",$moteur->texte));
				//enregistre l'item dans le document
				$chansons->merge();
			}
						
			
			if(!$getOdf){
				//calcul le temps d'execution
				$temps_fin = microtime(true);
				echo ' Temps d\'execution : '.round($temps_fin - $temps_debut, 4)." s. ";
				$nomFic = "texte_".$id."_".($nbFic+$itr);
				SaveFile(ROOT_PATH.'/data/capture/'.$nomFic.".txt",str_replace("<br/>","\n",$moteur->texte));
				echo " <a href='".WEB_ROOT.'/data/capture/'.$nomFic.".txt'>".$nomFic."</a>";
				SaveFile(ROOT_PATH.'/data/capture/'.$nomFic."_detail.html",$moteur->detail);
				echo " <a href='".WEB_ROOT.'/data/capture/'.$nomFic."_detail.html'> detail</a><br/>";
			}
		}catch (Exception $e) {
			echo "Récupère exception: " . get_class($e) . "<br/>";
		    echo "Message: " . $e->getMessage() . "<br/>";
		}
			
	}
	//finalise le document
	if($getOdf){
		$odf->mergeSegment($chansons);
		$odf->exportAsAttachedFile();
	}else{
		echo "<br/><br/>FIN DE GENERATION DES FICHIERS ".$itr." sur ".$nb;
	}
		
}catch (Exception $e) {
    echo "Message: " . $e->getMessage() . "\n";
}
	
function SaveFile($path,$texte){

	$fic = fopen($path, "w");
	if($fic){
		fwrite($fic, $texte);		
    	fclose($fic);
	}

}

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

function count_files($folder, $ext, $subfolders)
{
	//merci à http://www.phpsources.org/scripts51-PHP.htm
	
     // on rajoute le / à la fin du nom du dossier s'il ne l'est pas
     if(substr($folder, -1) != '/')
        $folder .= '/';
     
     // $ext est un tableau?
     $array = 0;
     if(is_array($ext))
        $array = 1;

     // ouverture du répertoire
     $rep = @opendir($folder);
     if(!$rep)
        return -1;
        
     $nb_files = 0;
     // tant qu'il y a des fichiers
     while($file = readdir($rep))
     {
        // répertoires . et ..
        if($file == '.' || $file == '..')
         continue;
        
        // si c'est un répertoire et qu'on peut le lister
        if(is_dir($folder . $file) && $subfolders)
            // on appelle la fonction
         $nb_files += count_files($folder . $file, $ext, 1);
        // vérification de l'extension avec $array = 0
        else if(!$array && substr($file, -strlen($ext))== $ext)
         $nb_files++;
        // vérification de l'extension avec $array = 1   
        else if($array==1 && in_array(strtolower(substr(strrchr($file,"."),1)), $ext))
         $nb_files++;
     }
     
     // fermeture du rep
     closedir($rep);
     return $nb_files;
} 


?>	