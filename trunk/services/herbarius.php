<?php
set_time_limit(1000);
ini_set("memory_limit",'1600M');

$temps_debut = microtime(true);


require_once('../public/config.php');

$ids = array(
	array("id"=>61570, "titre"=> "0.Le Lilus Arythmeticus")
	,array("id"=>61571, "titre"=> "1.La Polygonum")
	,array("id"=>61572, "titre"=> "2.La Silène luminaris")
	,array("id"=>61573, "titre"=> "3.La Bella Donna")
	,array("id"=>61562, "titre"=> "2.Pychsellis Vipérine")
	,array("id"=>61563, "titre"=> "3.Oxalis de Thalès")
	,array("id"=>61564, "titre"=> "4.La Trifolia Sadica")
	,array("id"=>61565, "titre"=> "5.La Purple Haze")
	,array("id"=>61566, "titre"=> "6.L'Alchemille dentelée")
	,array("id"=>61567, "titre"=> "7.La Dracocéphale")
	,array("id"=>61568, "titre"=> "8.La Reographia Lucifera")
	,array("id"=>61569, "titre"=> "9.La Pixacantha")
	);	
if(isset($_GET['id'])){
	$num = $_GET['id'];
}else{
	$num = mt_rand(0, 10);
}
$id = $ids[$num]["id"];		

$getHtml = true;
	
if(isset($_GET['nb']))
	$nb = $_GET['nb'];
else
	$nb = 1;

if(isset($_GET['err']))
	$err = true;
else
	$err = false;

if(isset($_GET['dicos']))
	$dicos = $_GET['dicos'];
else
	$dicos = "34,37";
	
	
if($getHtml)header ('Content-type: text/html; charset=utf-8');

	
try {

	$application->bootstrap();


	//récupère le nombre de fichier
	$nbFic = count_files(ROOT_PATH.'/data/capture/','txt','');
	
	$arrDicos = array(
		"concepts"=>$dicos
		,"syntagmes"=>4
		,"pronoms_complement"=>13
		,"conjugaisons"=>25
		,"pronoms"=>"13,14"
		,"déterminants"=>15
		,"negations"=>16);
	
	//récupère la définition d'une plante
	$dbConcepts = new Model_DbTable_Concepts();
	$Rowset = $dbConcepts->find($id);
	$parent = $Rowset->current();

	//récupère les généreteurs
	$defPlantes = $parent->findManyToManyRowset('Model_DbTable_Generateurs','Model_DbTable_ConceptsGenerateurs');
	$arrPlantes = $defPlantes->toArray();
	
	//nombre de plante possible
	$nbPlante = count($arrPlantes)-1;
	
	for ($itr = 0; $itr < $nb; $itr++) {
		try {
			$num = mt_rand(0, $nbPlante);
			$plante = $arrPlantes[$num];
			
			if($err){
				echo "<br/>".$itr." sur ".$nb;
				echo " texte ".$num." sur ".$nbChan;
			}
			
			//calcul une plante
			$moteur = new Gen_Moteur();
			$moteur->arrDicos = $arrDicos;	
			//$moteur->finLigne = "\r\n";
			$moteur->showErr = $err;
			if($err)echo "<br/>".$plante['valeur']."<br/>";

			$moteur->Generation($plante['valeur']);	
			
			if($getHtml){
				echo "<br/>".$moteur->texte."<br/>";
			}
			
			if($err){
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
		    echo print_r($moteur->arrClass);
		    echo $moteur->detail;
		}
			
	}
	//finalise le document
	if($err){
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