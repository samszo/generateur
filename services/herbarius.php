<?php
set_time_limit(1000);
ini_set("memory_limit",'1600M');

$temps_debut = microtime(true);


require_once('../public/config.php');

if(isset($_GET['langue'])){
	$langue = $_GET['langue'];
}else{
	$langue = "fr";
}

if($langue=="fr"){
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
	$arrDicos = array(
		"concepts"=>"34,37"
		,"syntagmes"=>4
		,"pronoms_complement"=>13
		,"conjugaisons"=>25
		,"pronoms"=>"13,14"
		,"déterminants"=>15
		,"negations"=>16);
		
}
if($langue=="en"){
	$ids = array(
		array("id"=>84213, "titre"=> "0.Lilus Arythmeticus")
		,array("id"=>84214, "titre"=> "1.Polygonum")
		,array("id"=>84215, "titre"=> "2.Silene luminaris")
		,array("id"=>84216, "titre"=> "3.Bella Donna")
		,array("id"=>84205, "titre"=> "2.Viperin Pychsellis")
		,array("id"=>84206, "titre"=> "3.Thales' Oxalis")
		,array("id"=>84207, "titre"=> "4.Trifolia Sadica")
		,array("id"=>84208, "titre"=> "5.Purple Haze")
		,array("id"=>84209, "titre"=> "6.Serrated Alchemilla")
		,array("id"=>84210, "titre"=> "7.Dragonhead")
		,array("id"=>84211, "titre"=> "8.Reographia Lucifera")
		,array("id"=>84212, "titre"=> "9.Pixacantha")
		);
	$arrDicos = array(
		"concepts"=>"42,65"
		,"syntagmes"=>41
		,"pronoms_complement"=>13
		,"conjugaisons"=>40
		,"pronoms"=>"38,14"
		,"déterminants"=>39
		,"negations"=>16);
}
if($langue=="es"){
	$ids = array(
		array("id"=>91588, "titre"=> "0.Lágrimas")
		,array("id"=>91589, "titre"=> "1.Polygonatum")
		,array("id"=>91579, "titre"=> "2.Amaranthus Rivea")
		,array("id"=>91580, "titre"=> "3.Dionaea muscipula")
		,array("id"=>91590, "titre"=> "2.La Uña de gato")
		,array("id"=>91591, "titre"=> "3.Peyotl")
		,array("id"=>91581, "titre"=> "4.Anthurium")
		,array("id"=>91582, "titre"=> "5.Verónica")
		,array("id"=>91583, "titre"=> "6.La Prosera Borgeana")
		,array("id"=>91584, "titre"=> "7.La Cleome Spinosa")
		,array("id"=>91585, "titre"=> "8.Capanula Barbata")
		,array("id"=>91586, "titre"=> "9.Physocarpus")
		);		
	$arrDicos = array(
		"concepts"=>"71,66"
		,"syntagmes"=>68
		,"pronoms_complement"=>13
		,"conjugaisons"=>67
		,"pronoms"=>"70,14"
		,"déterminants"=>69
		,"negations"=>16);
}

if(isset($_GET['id'])){
	$num = $_GET['id'];
}else{
	$num = mt_rand(0, 11);
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
	
if($getHtml)header ('Content-type: text/html; charset=utf-8');

	
try {

	$application->bootstrap();


	//récupère le nombre de fichier
	$nbFic = count_files(ROOT_PATH.'/data/capture/','txt','');
			
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
				echo '<br/> Temps d\'execution : '.round($temps_fin - $temps_debut, 4)." s. ";
				$nomFic = "texte_".$id."_".($nbFic+$itr);
				SaveFile(ROOT_PATH.'/data/herbarius/'.$nomFic.".html",str_replace("<br/>","\n",$moteur->texte));
				echo " <a href='".WEB_ROOT.'/data/herbarius/'.$nomFic.".html'>".$nomFic."</a>";
				SaveFile(ROOT_PATH.'/data/herbarius/'.$nomFic."_detail.html",$moteur->detail);
				echo " <a href='".WEB_ROOT.'/data/herbarius/'.$nomFic."_detail.html'> detail</a><br/>";
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