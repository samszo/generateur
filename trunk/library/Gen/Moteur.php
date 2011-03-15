<?php
/**
 * Generateur Framework
 *
 * LICENSE
 *
 * This source file is subject to the Artistic/GPL license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.generateur.com/license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@generateur.com so we can send you a copy immediately.
 *
 * @category   Generateur
 * @package    Moteur
 * @copyright  Copyright (c) 2010 J-P Balpe (http://www.balpe.com)
 * @license    http://framework.generateur.com/license/Artistic/GPL License
 * @version    $Id: Moteur.php 0 2010-09-20 15:26:09Z samszo $
 */

/**
 * Concrete class for generating Fragment for Generateur.
 *
 * @category   Generateur
 * @package    Moteur
 * @copyright  Copyright (c) 2010 J-P Balpe (http://www.balpe.com)
 * @license    http://framework.generateur.com/license/Artistic/GPL License
 */

class Gen_Moteur
{

	var $xmlDesc;
	var $texte;
	var $class;
	var $arrSegment;
	var $arrClass;
	var $arrDicos;
	var $ordre;
	var $segment;
	var $potentiel=0;
	var $detail;
	var $typeChoix = "alea";
	var $cache;
	
	/**
	 * Le constructeur initialise le moteur.
	 * 	 */
	public function __construct($urlDesc="") {

		if($urlDesc=="")$urlDesc=APPLICATION_PATH.'/configs/LangageDescripteur.xml';
		$this->xmlDesc = simplexml_load_file($urlDesc);	
			
	}

	public function Generation($texte, $getTexte=true, $cache=false){
		
		if(!$cache){
			$frontendOptions = array(
		    	'lifetime' => 31536000, // temps de vie du cache de 1 an
		        'automatic_serialization' => true
			);
		   	$backendOptions = array(
				// Répertoire où stocker les fichiers de cache
		        'cache_dir' => ROOT_PATH.'/tmp/'
			);
			// créer un objet Zend_Cache_Core
			$this->cache = Zend_Cache::factory('Core','File',$frontendOptions,$backendOptions);		
		}else{
			$this->cache = $cache;		
		}
		
		$this->arrClass = array();
		$this->ordre = 0;
		$this->segment = 0;
		$this->arrClass[$this->ordre]["generation"][] = $texte;
		
		//parcourt l'ensemble de la chaine
		for($i = 0; $i < strlen($texte); $i++)
        {
        	$this->ordre ++;
        	$c = $texte[$i];
        	if($c == "["){
        		//c'est le début d'une classe
        		//on récupère la valeur de la classe et la position des caractères dans la chaine
        		$i = $this->traiteClass($texte, $i);
        	}elseif($c == "="){
        		//c'est le début d'une notification de format
        		$i = $this->traiteFormat($texte, $i);
        	}elseif($c == "F"){
        		if($texte[$i+1]=="F"){
	        		//c'est la fin du segment
			        $this->arrSegment[$this->segment]["ordreFin"]= $this->ordre;
					$this->segment ++;
	        		$i++;
	        	}else{
	        		//on ajoute le caractère
					$this->arrClass[$this->ordre]["texte"] = $c;
	        	}
        	}else{
        		//on ajoute le caractère
				$this->arrClass[$this->ordre]["texte"] = $c;
        	}
        }
                
        //on calcule le texte
        if($getTexte){
        	$this->genereTexte();
        }

	}
		
	public function genereTexte(){

		$this->texte = "";
		$txtCondi = true;
		
		//vérifie la présence de segments
		if(count($this->arrSegment)>0){
			//choix aleatoire d'un segment
			$a = rand(0, count($this->arrSegment)-1); 			
			$ordreDeb = $this->arrSegment[$a]["ordreDeb"];
			$ordreFin = $this->arrSegment[$a]["ordreFin"];
		}else{
			$ordreDeb = 0;
			$ordreFin = count($this->arrClass);
		}
		
		for ($i = $ordreDeb; $i < $ordreFin; $i++) {
			$this->ordre = $i;
			$texte = "";
			$arr = $this->arrClass[$i];
			if(isset($arr["texte"])){
				//vérifie le texte conditionnel
				if($arr["texte"]=="<"){
			        //choisi s'il faut afficher
			        $this->potentiel ++;
			        $a = rand(0, 1000);
			        if($a>500){
			        	$txtCondi = false;
			        }
				}elseif($arr["texte"]==">"){
			        $txtCondi = true;
				}elseif($txtCondi){
					if($arr["texte"]=="%"){
						$texte .= "<br/>";	
					}else{
						$texte .= $arr["texte"];
					}
				}
			}else{
				if($txtCondi){
					$det = "";
					$sub = "";
					$adjs = "";
					$verbe = "";
					 
					if(isset($arr["determinant"])){
						$det = $this->genereDeterminant($arr);					
					}
					
					if(isset($arr["substantif"])){					
						$sub = $this->genereSubstantif($arr);
					}					
					
					if(isset($arr["adjectifs"])){					
						foreach($arr["adjectifs"] as $adj){
							$adjs .= $this->genereAdjectif($arr, $adj);
						}
					}
	
					if(isset($arr["verbe"])){					
						$verbe = $this->genereVerbe($arr);
					}					
					
					if(isset($arr["syntagme"])){					
						$texte .= $arr["syntagme"];
					}					
										
					$texte .= $det.$sub." ".$adjs.$verbe;
				}					
			}
			$this->arrClass[$i]["texte"] = $texte;
			$this->texte .= $texte;
		}
		//mise en forme du texte
		$LT = strlen($this->texte);
		//mise en forme poésie
		
		//création du tableau de génération
		$this->detail = $this->arrayVersHTML($this->arrClass);
		
		
	}
	
	public function genereSubstantif($arr){

		$txt = $arr["substantif"]["s"];
		
		if($arr["pluriel"]){												
			$txt = $arr["substantif"]["p"];
		}
		
		$txt = $arr["substantif"]["prefix"].$txt;
				
		return $txt;
	}
	
	public function generePronom($arr){
		
		//vérifie la présence d'un d&terminant
		if(!isset($arr["determinant_verbe"])){
			//3eme personne sans pronom
			$arr["prosuj"] = "";
			$arr["terminaison"] = 3;
		}else{
			if($arr["determinant_verbe"][6]!=0){
				//pronom indéfinie
				$arr["prosuj"] = $this->getPronom($arr["determinant_verbe"][6],"sujet_indefini");
				$arr["terminaison"] = 3;
			}else{
				//pronom sujet			
				if($arr["determinant_verbe"][2]==0){
					//pas de pronom
					$arr["prosuj"] = "";
					$arr["terminaison"] = 3;				
				}else{
					//pronom définie
					$numP = $arr["determinant_verbe"][2];
					$pr = "";
					//définition des terminaisons et du pluriel
					if($numP==6){
						//il/elle singulier
						$pr = "[a_il]";
						$pluriel = false;
						$arr["terminaison"] = 3;				
					}	
					if($numP==7){
						//il/elle pluriel
						$pr = "[a_il]";
						$pluriel = true;
						$arr["terminaison"] = 6;				
					}	
					if($numP==8){
						//pas de pronom singulier
						$pr = "[a_zéro]";
						$pluriel = false;
						$arr["terminaison"] = 3;				
					}	
					if($numP==9){
						//pas de pronom pluriel
						$pr = "[a_zéro]";
						$pluriel = true;
						$arr["terminaison"] = 6;				
					}
					if($numP==1 || $numP==2){
						$pluriel = false;
						$arr["terminaison"] = $numP;				
					}
					if($numP==4 || $numP==5){
						$pluriel = true;
						$arr["terminaison"] = $numP;				
					}
					
					if($numP>=6){	
						//calcul le vecteur
						//nombre d’informations sur le vecteur - valeur indiquée + 1
						$numSub = count($this->arrClass["vecteur"])-$arr["determinant_verbe"][7]+1;
						//récupère le genre
						$genre = $this->arrClass["vecteur"][$numSub]["genre"];     	
						//génère le pronom
						$m = new Gen_Moteur();
						$m->arrDicos = $this->arrDicos;
						$m->Generation($pr,false,$this->cache);
						$m->arrClass[1]["genre"]=$genre;
						$m->arrClass[1]["pluriel"]=$pluriel;						
						$this->potentiel += $m->potentiel;						
						$arr["prosuj"] = $m->genereTexte();
					}else{
						$arr["prosuj"] = $this->getPronom($numP,"sujet");
					}
				}
			}
			//pronom complément
			if($arr["determinant_verbe"][3]!=0 && $arr["determinant_verbe"][4]!=0){
				$numPC = $arr["determinant_verbe"][3].$arr["determinant_verbe"][4];
				$arr["prodem"] = $this->getPronom($numPC,"complément");
			}			
		}		
		return $arr;
	}
	
	public function genereDeterminant($arr){

		$det = "";
		
		if(isset($arr["genre"])){			
			//calcul le déterminant
			if($arr["elision"]==0 && $arr["genre"]==1){
				$det = $arr["determinant"][0]["lib"]." ";
			}
			if($arr["elision"]==0 && $arr["genre"]==2){
				$det = $arr["determinant"][1]["lib"]." ";
			}
			if($arr["elision"]==1 && $arr["genre"]==1){
				$det = $arr["determinant"][2]["lib"];
			}
			if($arr["elision"]==1 && $arr["genre"]==2){
				$det = $arr["determinant"][3]["lib"];
			}
		}
		
		return $det;
	}
	
	public function genereAdjectif($arr,$adj){

		$txt = "";

		//calcul le nombre
		$n = "_s"; 		
		if($arr["pluriel"]){												
			$n = "_p";
		}
		
		//calcul le genre
		$g = "m"; 		
		if(isset($arr["genre"])){												
			if($arr["genre"]==2) $g = "f";
		}

		$txt = $adj["prefix"].$adj[$g.$n];

		return $txt;
	}

	public function genereTerminaison($arr){
		
		//par défaut la terminaison est 3eme personne du présent
		$num = 2;
		
		if(isset($arr["determinant_verbe"])){
			$temps= $arr["determinant_verbe"][1];
			
			if($arr["determinant_verbe"][1]==1){
				$temps= 0;
			}
			if($arr["determinant_verbe"][1]==7){
				$temps= 0;
			}
			
			$num = ($temps*6)+$arr["terminaison"]-1; 		
			if($arr["determinant_verbe"][1]==8){
				$num= 36;
			}
			if($arr["determinant_verbe"][1]==9){
				$num= 37;
			}
		}	
					
		$txt = $this->getTerminaison($arr["verbe"]["id_conj"],$num);

		return $txt;
	}
	
	public function getCarac($cls){
		
		if(is_string($cls)){
			$this->arrClass[$this->ordre]["texte"] = $cls;			
		}else{
			if(isset($cls["id_adj"])){
				$this->arrClass[$this->ordre]["adjectifs"][] = $cls;
			}
			if(isset($cls["id_dtm"])){
				$this->arrClass[$this->ordre]["adjectifs"][] = $cls;
			}
			if(isset($cls["id_sub"])){
				$this->getSubstantif("",$cls);
			}
			if(isset($cls["id_verbe"])){
				$this->getVerbe("",$cls);
			}			
		}		
		
	}

	
	public function genereVerbe($arr){

		/*
		Position 1 : type de négation
		Position 2 : temps verbal
		Position 3 : pronoms sujets définis
		Positions 4 ET 5 : pronoms compléments
		Position 6 : ordre des pronoms sujets
		Position 7 : pronoms indéfinis
		Position 8 : Place du sujet dans la chaîne grammaticale
		*/
		$arr["debneg"]="";
		$arr["finneg"]="";
		$arr["prodem"]="";		
				
		//génère le pronom
		$arr = $this->generePronom($arr);    	
		
		//récupère la terminaison
		$term = $this->genereTerminaison($arr);
		
		//construction du centre
		$centre = $arr["verbe"]["prefix"].$term;
		
		//construction de l'élision
		$eli = $arr["verbe"]["elision"];
		if($eli==0){
			$arrEli = array("a", "e", "é", "ê", "i","y");
			$arr["elision"] = in_array(substr($centre,0,1), $arrEli);
		}
		
		$verbe="";
		if(isset($arr["determinant_verbe"])){
			//génère la négation
			if($arr["determinant_verbe"][0]!=0){
				$arr["finneg"] = $this->getNegation($arr["determinant_verbe"][0]);
				if($arr["elision"]==0){
					$arr["debneg"] = "ne ";	
				}else{
					$arr["debneg"] = "n'";
				}
			}
			
			//construction de la forme verbale
			$verbe = "";
			//gextion de l'infinitif
			if($arr["determinant_verbe"][1]==9){
				$verbe = $centre;
				if($arr["prodem"]!=""){
					if($eli==0){
						$verbe = $arr["prodem"]["lib"].$verbe; 
					}else{
						$verbe = $arr["prodem"]["lib_eli"].$verbe; 
					}
					$eli=0;
				}	
				$verbe = $arr["finneg"].$verbe; 
				if($arr["debneg"]!=""){
					$verbe = $arr["debneg"].$verbe; 
				}	
			}		
			//gestion de l'ordre inverse
			if($arr["determinant_verbe"][6]==1){
				$verbe = $centre."-";
				$c = substr($centre,strlen($centre)-1);
				if(($c == "e" || $c == "a") && $arr["terminaison"]==3){
					$verbe .= "t-"; 
				}
				if($c == "e" && $arr["terminaison"]==1){
					$verbe = substr($centre,-1)."é-"; 
				}
				$verbe = $arr["debneg"]." ".$arr["prodem"]." ".$verbe." ".$arr["prosuj"]["lib"].$arr["prodem"]["lib"];
			}
		}
		//gestion de l'ordre normal
		if($verbe==""){
			$verbe = $centre.$arr["finneg"];
			if($arr["prodem"]!=""){
				if($eli==0){
					$verbe = $arr["prodem"]["lib"].$verbe; 
				}else{
					$verbe = $arr["prodem"]["lib_eli"].$verbe; 
					$eli=0;
				}
			}	
			if($arr["debneg"]!=""){
				$verbe = $arr["debneg"].$verbe; 
			}	
			if($arr["prosuj"]!=""){
				if($eli==0){
					$verbe = $arr["prosuj"]["lib"].$verbe; 
				}else{
					$verbe = $arr["prosuj"]["lib_eli"].$verbe; 
				}
			}	
		}
		
		return $verbe;
	}
	
	public function getClass($class){

		$this->arrClass[$this->ordre]["class"][] = $class;

		//vérifie si la class est un déterminant
		if(is_numeric($class)){
			$this->getDeterminant($class);
		}
		
		//vérifie si la class possède un déterminant de class
		$c = strpos($class,"_");
		if($c>0){
			$arr = explode("_",$class);			
			switch ($arr[0]) {
				case "a":
					$this->getAdjectifs($class);
					break;
				case "m":
					$this->getSubstantif($class);
					break;
				case "v":
					$this->getVerbe($class);
					break;
				case "s":
					$this->getSyntagme($class,false);
					break;
			}
		}elseif(substr($class,0,5)=="carac"){
			//la class est un caractère
			$classSpe = str_replace("carac", "carac_", $class);
			$this->getClassSpe($classSpe);
		}else{
			//vérifie si la class possède un blocage d'information
			$c = strpos($class,"#");
			if($c>0){
				$classSpe = str_replace("#","",$class); 
				$this->getSyntagme($classSpe);	
			}
	
			//vérifie si la class possède un blocage d'information
			if(substr($class,0,1)=="="){
				$this->getBlocage($class);	
			}
			
			//vérifie si la class est un type spécifique
			$c = strpos($class,"-");
			if($c>0){
				$classSpe = substr($class,0,$c)."_".substr($class,$c+1);
				$this->getClassSpe($classSpe);
			}			
		}
						
	}

	public function getClassSpe($class){

		$cls = $this->getAleaClass($class);
		if(is_string($cls)){
			$this->arrClass[$this->ordre]["texte"] = $cls;			
		}elseif(get_class($cls)=="Gen_Moteur"){
			$this->getClassMoteur($cls);
		}else{
			$this->getClassType($cls);
		}		
	}

	public function getClassMoteur($moteur){

		//ajoute le class générée
		foreach($moteur->arrClass as $k=>$c){
			if($k==="vecteur"){
				foreach($c as $v){
					$this->arrClass["vecteur"][] = $v;
				}
			}else{
				$this->ordre ++;				
				$this->arrClass[$this->ordre] = $c;
			}
		}
	}
	
	
	public function getClassType($cls){

		if(isset($cls["id_conj"])){
		    $this->arrClass[$this->ordre]["adjectifs"][] = $cls;
		}
		if(isset($cls["id_sub"])){
		    $this->getSubstantif("",$cls);
		}
		if(isset($cls["id_verbe"])){
		    $this->getVerbe("",$cls);
		}
	}
	
	public function getClassVals($txt,$i=0){

		if(!$txt){
	        return array("deb"=>$i,"fin"=>$i,"valeur"=>$txt,"arr"=>$arr);
		}
		
		$deb = strpos($txt,"[",$i);
		$fin = strpos($txt,"]",$deb+1);
		
		//dan sle cas de ado par exemple = pas de crochet
		if($deb===false){
	        $class = $txt;
		}else{
			//on récupère la valeur de la classe
	        $class = substr($txt, $deb+1, -(strlen($txt)-$fin));
		}
		$this->arrClass[$this->ordre]["class"][] = $class;
        
        
        //on récupère la définition de l'accord 
        $arr = explode("||",$class);
	    $class = $arr[0];        	 	
        if(count($arr)>1){
	        $this->arrClass[$this->ordre]["accord"] = $arr[1];
        }
        
        //on récupère le tableau des class
        $arr = explode("|",$class);        		        
        
        return array("deb"=>$deb,"fin"=>$fin,"valeur"=>$class,"arr"=>$arr);
	}
	
	public function getAdjectifs($class){

        //récupère le substantif
        $arr=explode("@", $class);
        if(count($arr)>1){ 
        	$this->getSubstantif($arr[1]);
        }
		
        $d = strpos($arr[0],"∏");        
        if($d){        	
        	//récupère les adjectifs
	        $arrAdj=explode("∏", $arr[0]);        
	        //récupère la définition des adjectifs
	        foreach($arrAdj as $a){
		        $this->arrClass[$this->ordre]["adjectifs"][] = $this->getAleaClass($a);
	        }        	
        }else{
	        $this->arrClass[$this->ordre]["adjectifs"][] = $this->getAleaClass($arr[0]);        	
        }
	}
	
	public function getBlocage($class){

        //récupère le numéro du blocage
        $num=substr($class,1);
		
        if($num=="x" || !isset($this->arrClass["vecteur"])){
        	//on applique le masculin singulier
	        $this->arrClass[$this->ordre]["pluriel"] = false; 
	        $this->arrClass[$this->ordre]["genre"] = 1;
			$this->arrClass["vecteur"][] = array("pluriel"=>$this->arrClass[$this->ordre]["pluriel"]
	        	,"genre"=>$this->arrClass[$this->ordre]["genre"]);       	                 	
        }else{
	        //récupère l'ordre
	        $ordre=count($this->arrClass["vecteur"])-$num;        
	        //Récupère les informations de genre et de nombre
        	if(!isset($this->arrClass["vecteur"][$ordre]["pluriel"])){
        		$toto = 1;
        	}
	        
	        $this->arrClass[$this->ordre]["pluriel"] = $this->arrClass["vecteur"][$ordre]["pluriel"]; 
	        $this->arrClass[$this->ordre]["genre"] = $this->arrClass["vecteur"][$ordre]["genre"];         	
        }
        
        
	}
	
	public function getDeterminant($class){

        $arrClass = false;

        //vérifie si le déterminant est pour un verbe
        if(strlen($class) > 6){
        	$intD = intval($class);
        	if($intD==0 && $this->ordre > 0){
        		//vérifie si le determinant n'est pas transmis
        		$strD = $this->arrClass[$this->ordre]["determinant_verbe"];
        		for($i = $this->ordre-1; $i >= 0; $i--){
        			if(intval($this->arrClass[$i]["determinant_verbe"])!=0){
						$class = $this->arrClass[$i]["determinant_verbe"];
						$i=-1;        				
        			}
        		}
        	}
			$this->arrClass[$this->ordre]["determinant_verbe"] = $class;
	        return $class;
        }       	
        
        //vérifie s'il faut chercher le pluriel
        $pluriel = false;
        if($class > 50){
        	$pluriel = true;
        	$class = $class-50;
        }       			
        //vérifie s'il faut chercher le déterminant
        if($class!=99 && $class!=0){
        	$c = md5("getDeterminant_".$this->arrDicos["déterminants"]."_".$class."_".$pluriel);
			if(!$arrClass = $this->cache->load($c)) {
		        $tDtr = new Model_DbTable_Determinants();
	        	$arrClass = $tDtr->obtenirDeterminantByDicoNumNombre($this->arrDicos["déterminants"],$class,$pluriel);        				
			    $this->cache->save($arrClass, $c);
			}
        }
        
        if($class==0){
        	//vérifie si le determinant n'est pas transmis
        	for($i = $this->ordre-1; $i > 0; $i--){
        		if(isset($this->arrClass[$i]["determinant"])){
	        		if(intval($this->arrClass[$i]["determinant"])!=0){
						$arrClass = $this->arrClass[$i]["determinant"];
						$pluriel = $this->arrClass[$i]["pluriel"];
						$i=-1;        				
	        		}
        		}
        	}
        }
        
        //ajoute le déterminant
		$this->arrClass[$this->ordre]["determinant"] = $arrClass;
        
		//met à jour le nombre
		$this->arrClass[$this->ordre]["pluriel"] = $pluriel; 
                
        return $arrClass;
	}
	
	public function getSubstantif($class, $arrClass=false){

        //récupération du substantif
        if(!$arrClass) $arrClass = $this->getAleaClass($class);

        if($arrClass){
	        //ajoute le substantif
	        $this->arrClass[$this->ordre]["substantif"] = $arrClass;
	
	        //met à jour le genre et l'élision
	        $this->arrClass[$this->ordre]["genre"] = $arrClass["genre"];
	        $this->arrClass[$this->ordre]["elision"] = $arrClass["elision"];
	        
	        //vérifie si un déterminant est présent
	        if(!isset($this->arrClass[$this->ordre]["pluriel"]))$this->arrClass[$this->ordre]["pluriel"]=false;
	        
	        //ajoute le vecteur
	        $this->arrClass["vecteur"][] = array("pluriel"=>$this->arrClass[$this->ordre]["pluriel"]
	        	,"genre"=>$this->arrClass[$this->ordre]["genre"]);
        }        
        return $arrClass;
	}

	public function getSyntagme($class, $direct=true){
		
		//vérifie si le syntagme direct #
		if($direct){

	        $c = md5("getSyntagme_".$this->arrDicos['syntagmes']."_".$class);
			if(!$arrClass = $this->cache->load($c)) {
	        	//récupère la définition de la class
	        	$table = new Model_DbTable_Syntagmes();
	        	$arrClass = $table->obtenirSyntagmeByDicoNum($this->arrDicos['syntagmes'],$class);
				$this->cache->save($arrClass,$c);
			}
	        	        
	        $syn = $arrClass["lib"];
	        if(substr($syn,0,1)== "["){
	        	$this->traiteClass($syn);
	        }else{
		        $this->arrClass[$this->ordre]["syntagme"] = $syn;
	        }
		}else{
	        $arrClass = $this->getAleaClass($class);
	        if(isset($arrClass["lib"])){
	        	$this->arrClass[$this->ordre]["syntagme"] = $arrClass["lib"];			
	        }else{
	        	$this->traiteClass($arrClass["valeur"]);
	        }
		}
	}
	
	public function traiteClass($class, $i=0){

		$arrClass = $this->getClassVals($class,$i);	        	
		foreach($arrClass["arr"] as $cls){
			$this->getClass($cls);
        }	        	
		return $arrClass["fin"];
	}
	
	public function traiteFormat($class, $i=0){

		$deb = $i+1;
		$fin = strpos($class,"$",$deb);

		//on récupère les valeurs de format
        $txt = substr($class, $deb, -(strlen($class)-$fin));

        //enregistre le numéro du segment
        $this->arrSegment[$this->segment]["num"] = $txt[0].$txt[1];
        //enregistre l'indice de répétition
        $this->arrSegment[$this->segment]["repetition"] = $txt[8];
        //enregistre l'ordre de départ
        $this->arrSegment[$this->segment]["ordreDeb"]= $this->ordre;
        
        //vérifie si le texte est en prose ou en poésie
        $forme = $txt[9].$txt[10];
        if($forme=="00"){
			$this->arrClass[$this->ordre]["format"]["page"] = "prose";        	
        }
        if($forme=="01"){
			$this->arrClass[$this->ordre]["format"]["page"] = "poésie en vers-régulier";        	
        }
        if($forme=="02"){
			$this->arrClass[$this->ordre]["format"]["page"] = "poésie en vers-défini";        	
        }
        if($forme=="XX"){
			$this->arrClass[$this->ordre]["format"]["page"] = "poésie en vers-libre";        	
        }
        if($forme=="03"){
			$this->arrClass[$this->ordre]["format"]["page"] = "poésie en vers définis centrés";        	
        }
        
        return $fin;
	}
	
	public function getNegation($class){

        $c = md5("getNegation_".$this->arrDicos['negations']."_".$class);
		if(!$arrClass = $this->cache->load($c)) {
        	//récupère la définition de la class
        	$table = new Model_DbTable_Negations();
        	$arrClass = $table->obtenirNegationByDicoNum($this->arrDicos['negations'],$class);
			$this->cache->save($arrClass,$c);
		}
        
        return $arrClass["lib"];
	}
	
	public function getPronom($class, $type){


        $c = md5("getPronom_".$this->arrDicos["pronoms"]."_".$class."_".$type);
		if(!$arrClass = $this->cache->load($c)) {
			//récupère la définition de la class
       		$table = new Model_DbTable_Pronoms();
        	$arrClass = $table->obtenirPronomByDicoNumType($this->arrDicos['pronoms'],$class,$type);
			$this->cache->save($arrClass,$c);
		}

		return $arrClass;										

	}

	public function getTerminaison($idConj, $num){


        $c = md5("getTerminaison_".$idConj."_".$num);
		if(!$arrClass = $this->cache->load($c)) {
			//récupère la définition de la class
        	$table = new Model_DbTable_Terminaisons();
        	$arrClass = $table->obtenirConjugaisonByConjNum($idConj, $num);
			$this->cache->save($arrClass,$c);
		}
		
		if(get_class($arrClass)=="Exception"){
	        $this->arrClass[$this->ordre]["ERREUR"] = $arrClass->getMessage()."<br/><pre>".$arrClass->getTraceAsString()."</pre>";
			return "";
		}
		return $arrClass["lib"];
												
	}
	
	public function getVerbe($class, $arrClass=false){

        //récupération du verbe
        if(!$arrClass) $arrClass = $this->getAleaClass($class);
        
		if($arrClass){
	        //ajoute le verbe
	        $this->arrClass[$this->ordre]["verbe"] = $arrClass;
	                
	        //met à jour l'élision
	        if(count($arrClass["elision"])<1){
	        	$to = 1;
	        }
	        $this->arrClass[$this->ordre]["elision"] = $arrClass["elision"];
		}
		        
        return $arrClass;
	}
	
	public function getAleaClass($class){
		
        //cherche la définition de la class
        $arrCpt = $this->getClassDef($class);
        
        //cas des classes théoriques et des erreurs
        if(count($arrCpt["dst"])<1){
        	return false;
        }
        
        //enregistre le potentiel
        $this->potentiel += count($arrCpt["dst"]);
        
        if($this->typeChoix=="tout"){
        	$cpt ="";
        	foreach($arrCpt["dst"] as $dst){
        		$this->getClassGen($dst);	
        	}
        }else{
	        //choisi un concept aléatoirement
	        $a = rand(0, count($arrCpt["dst"])-1);        
	        $cpt = $this->getClassGen($arrCpt["dst"][$a]);
        }
                	
		return $cpt; 			
		
	}

	public function getClassGen($cpt){
		
        //Vérifie si le concept est un générateur
        if(isset($cpt["id_gen"])){
        	//générer l'expression
			$m = new Gen_Moteur();
			$m->arrDicos = $this->arrDicos;		
			//génére la classe
			$m->Generation($cpt['valeur'],false,$this->cache);
			//récupère les class générée
			$this->getClassMoteur($m);
			$this->potentiel += $m->potentiel;
			$cpt = false;
		}
		
		return $cpt; 			
		
	}
	
	public function getClassDef($class){


        $c = md5("getClassDef_".$this->arrDicos['concepts']."_".$class);
		if(!$arrCpt = $this->cache->load($c)) {
			//récupère la définition de la class
			$arrClass=explode("_", $class);
	        $table = new Model_DbTable_Concepts();	
	   	    $arrCpt = $table->obtenirConceptDescription($this->arrDicos['concepts'],$arrClass);
			$this->cache->save($arrCpt,$c);
		}
		
		if(get_class($arrCpt)=="Exception"){
    		$this->arrClass[$this->ordre]["ERREUR"] = $arrCpt->getMessage()."<br/><pre>".$arrCpt->getTraceAsString()."</pre>";
    		$arrCpt = false;
		}		
        return $arrCpt;
	}

	
	
	public function traiteAction($chaine, $action){

		//applique l'action défini dans la description du langage
		switch ($action['type']) {
			case 'explode':
				//pour améliorer le explode des saut de ligne
				$c = str_replace("\\r","- -",$action['char']);
				//met la chaine dans un tableau
				$arr = explode($c, $chaine);
				//boucle sur les fragments de chaine optenus
				for ($i = 0 ; $i < count($arr); $i++) {
					//foreach ($arr as $frag) {
					$frag = $arr[$i];
					//vérifie si le traitement comporte des sous actions
					if(count($action->children())==0){
						$this->AjoutFrag($frag, $action);
					}else{
						//boucle sur les sous actions de l'action
						foreach ($action->children() as $act) {
							//vérifie si l'action est liée à la fin du tableau
							switch ($act['type']) {
								case "VerifFin":
									if($i==(count($arr)-1)){
										$this->traiteAction($frag, $act->action);					
									}else{
										$this->traiteAction($frag, $act->NoVerifAction);					
									}
									break;								
								case "VerifDeb":
									if($i==0){
										$this->traiteAction($frag, $act->action);					
									}else{
										$this->traiteAction($frag, $act->NoVerifAction);					
									}
									break;								
								default:
									$this->traiteAction($frag, $act);								
									break;
							}							
						}
					}
				}
				break;				
			case 'VerifSubstr':
				$c = substr($chaine, $action['deb'],$action['length']);
				if($c==$action['val']){
					//on exécute l'action suivante
					$this->traiteAction($chaine, $action->action);					
				}else{
					$this->traiteAction($chaine, $action->NoVerifAction);					
				}
				break;
		}

	}	


	function arrayVersHTML($tab, $col1 = "Cl&eacute;", $col2 = "Valeur", $bordure = 1)
	{
 		//http://www.phpsources.org/scripts471-PHP.htm
 		//modifier pour prendre en compte récursivement les tableaux   
 	    /* le chiffre doit être positif */
	    $bordure = (int) $bordure;
	    if ($bordure < 1) $bordure = 1;
	
	    /* le style CSS 
	    (rappel : il est préférable d'utiliser une feuiille externe plutôt que des styles internes aux balises) */
	    $style = "border: {$bordure}px solid black;font-style: bold;color:black;"; // les accolades permettent de coller la valeur numérique à "px"
		$styleErr = "border: {$bordure}px solid black;font-style: bold;color:red;";
		$styleTxt = "border: {$bordure}px solid black;font-style: bold;color:green;";
		
		/* génération de la première ligne, avec les libellés balisés comme cellules d'entête */
	    /* explications sur le scope="col" : l'accessibilité, lire l'article http://www.pompage.net/pompe/autableau/ */
	    $aafficher = "<table style='border-collapse: collapse; $style'>\n<tr>
	    <th scope='col' style='$style'>$col1</th> 
	    <th scope='col' style='$style'>$col2</th>\n</tr>\n";
	    
	    /* génération de chaque ligne ; col de gauche : la clé, celle de droite : la valeur correspondante
	    à chaque tour de boucle on ajoute le string généré à la suite du précédent (opérateur .=) */
	    foreach($tab as $cle => $valeur)
	    {
	        $aafficher .= "<tr style='$style'>
			    <td style='$style'>$cle</td>";
		    if(is_array($valeur)){
		    	$valeur = $this->arrayVersHTML($valeur);
		    }
		    if($cle==="ERREUR"){
			    $aafficher .= "<td style='".$styleErr."'>$valeur</td>\n</tr>\n";
		    }elseif($cle==="texte"){
			    $aafficher .= "<td style='$styleTxt'>$valeur</td>\n</tr>\n";
		    }else{
			    $aafficher .= "<td style='$style'>$valeur</td>\n</tr>\n";
		    }
	    }
	    
	    /* on ferme le tableau HTML (nécessaire pour la validité) */
	    $aafficher .= "</table>\n";
	    
	    return $aafficher;
	}
	
	
}
?>