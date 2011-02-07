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
	var $arrClass;
	var $arrDicos;
	var $ordre;
	var $potentiel=0;
	var $detail;
	
	/**
	 * Le constructeur initialise le moteur.
	 * 	 */
	public function __construct($urlDesc="") {

		if($urlDesc=="")$urlDesc=APPLICATION_PATH.'/configs/LangageDescripteur.xml';
		$this->xmlDesc = simplexml_load_file($urlDesc);	
			
	}

	public function Generation($texte, $getTexte=true){
		
		$this->arrClass = array();
		$this->ordre = 0;
		$this->arrClass[$this->ordre]["generation"][] = $texte;
		
		//parcourt l'ensemble de la chaine
		for($i = 0; $i < strlen($texte); $i++)
        {
        	$this->ordre ++;
        	$c = $texte[$i];
        	//est-ce le début d'une classe
        	if($c == "["){
        		//on récupère la valeur de la classe et la position des caractères dans la chaine
        		$i = $this->traiteClass($texte, $i);
        	}else{
        		//on ajoute le caractère
				$this->arrClass[$this->ordre]["texte"] = $c;
        	}
        }
                
        //on calcule le texte
        if($getTexte){
        	$this->genereTexte();
        	$this->detail = $this->arrayVersHTML($this->arrClass);
        }

	}
		
	public function genereTexte(){

		$this->texte = "";
		$txtCondi = true;
		$this->ordre = 0;
		foreach($this->arrClass as $arr){
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
				}else{
					if($txtCondi){
						if($arr["texte"]=="%"){
							$this->texte .= "<br/>";	
						}else{
							$this->texte .= $arr["texte"];
						}
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
						$this->texte .= $arr["syntagme"];
					}					
										
					$this->texte .= $det.$sub." ".$adjs.$verbe;
				}					
			}
			$this->ordre ++;
		}
		
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
		
		if($arr["determinant_verbe"][6]!=0){
			//pronom indéfinie
			$arr["prosuj"] = $this->getPronom($arr["determinant_verbe"][6],"sujet_indefini");
			$arr["terminaison"] = 3;
		}else{
			if($arr["determinant_verbe"][2]!=0){
				//pronom définie
				$numP = $arr["determinant_verbe"][2];
				//gestion de la place du sujet
				if($numP>=6 && $numP<=9){
					//récupération des infos du vecteur de référence
				    if(isset($this->arrClass["vecteur"])){
				    	$i = $arr["determinant_verbe"][7];
			    		$pluriel = $this->arrClass["vecteur"][$i]["pluriel"];     	
			    		$genre = $this->arrClass["vecteur"][$i]["genre"];     	
					}
					//récupère la génération du pronom
					$m = new Gen_Moteur();
					$m->arrDicos = $this->arrDicos;		
					//précise le genre
					$m->arrClass[0]["genre"]=$genre;
					//précise le nombre	
					$m->arrClass[0]["genre"]=$pluriel;	
					//génére la classe
					$m->Generation($arrP["lib"]);
					//récupère le pronom
					$arr["prosuj"] = $m->texte;
					if($pluriel)					
						$arr["terminaison"] = 6;
					else
						$arr["terminaison"] = 3;
				}else{
					$arr["prosuj"] = $this->getPronom($numP,"sujet");
					$arr["terminaison"] = $numP;
				}
				//pronom complément
				if($arr["determinant_verbe"][3]!=0 && $arr["determinant_verbe"][4]!=0){
					$numPC = $arr["determinant_verbe"][3].$arr["determinant_verbe"][4];
					$arr["prodem"] = $this->getPronom($numPC,"complément");
				}
			}else{
				//vérifie si la class précédente est définie
				if($this->arrClass[$this->ordre-1]){
					//if()
				}else{
					//pronom par défault
					$arr["prosuj"] = $this->getPronom(3,"sujet");
					$arr["terminaison"] = 3;
				}
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
		
		//calcul le numéro de la conjugaison
		//if($arr["terminaison"]==7)
			
		//par défaut la terminaison est 3eme personne du présent
		if($num==-1)$num = 2;
		
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
			$eli = in_array(substr($centre,0,1), $arrEli);
		}

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
				default:
					$cls = $this->getAleaClass($class);
					if($cls)
						$this->arrClass[$this->ordre]["default"][] = $cls;
				break;
			}			
		}
		
		
		//vérifie si la class est un caractère
		if(substr($class,0,5)=="carac"){
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
		}elseif(isset($cls->arrClass)){
			//ajoute le class générée
			foreach($cls->arrClass as $c){
				$this->ordre ++;				
				$this->arrClass[$this->ordre] = $c;
			}
		}else{
			$this->getClassType($cls);
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

		$deb = strpos($txt,"[",$i);
		$fin = strpos($txt,"]",$deb+1);

		//on récupère la valeur de la classe
        $class = substr($txt, $deb+1, -(strlen($txt)-$fin));
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
		
        //récupère l'ordre
        $ordre=count($this->arrClass["vecteur"])-$num;
        
        //Récupère les information de genre et de nombre
        $this->arrClass[$this->ordre]["pluriel"] = $this->arrClass["vecteur"][$ordre]["pluriel"]; 
        $this->arrClass[$this->ordre]["genre"] = $this->arrClass["vecteur"][$ordre]["genre"]; 
        
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
	        $tDtr = new Model_DbTable_Determinants();
        	$arrClass = $tDtr->obtenirDeterminantByDicoNumNombre($this->arrDicos["déterminants"],$class,$pluriel);        				
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

        //ajoute le substantif
        $this->arrClass[$this->ordre]["substantif"] = $arrClass;

        //met à jour le genre et l'élision
        $this->arrClass[$this->ordre]["genre"] = $arrClass["genre"];
        $this->arrClass[$this->ordre]["elision"] = $arrClass["elision"];
        
        //vérifie si un déterminant est présent
        if(!$this->arrClass[$this->ordre]["pluriel"])$this->arrClass[$this->ordre]["pluriel"]=false;
        
        //ajoute le vecteur
        $this->arrClass["vecteur"][] = array("pluriel"=>$this->arrClass[$this->ordre]["pluriel"]
        	,"genre"=>$this->arrClass[$this->ordre]["genre"]);
        
        return $arrClass;
	}

	public function getSyntagme($class, $direct=true){
		
		//vérifie si le syntagme direct #
		if($direct){
	        //récupère la définition de la class
	        $table = new Model_DbTable_Syntagmes();
	        $arrClass = $table->obtenirSyntagmeByDicoNum($this->arrDicos['syntagmes'],$class);
			
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
	
	public function getNegation($class){

        //récupère la définition de la class
        $table = new Model_DbTable_Negations();
        $arrClass = $table->obtenirNegationByDicoNum($this->arrDicos['negations'],$class);
		
        return $arrClass["lib"];
	}
	
	public function getPronom($class, $type){

        //récupère la définition de la class
        $table = new Model_DbTable_Pronoms();
        $arrClass = $table->obtenirPronomByDicoNumType($this->arrDicos['pronoms'],$class,$type);

		return $arrClass;										

	}

	public function getTerminaison($idConj, $num){

        //récupère la définition de la class
        $table = new Model_DbTable_Terminaisons();
        $arrClass = $table->obtenirConjugaisonByConjNum($idConj, $num);

		return $arrClass["lib"];
												
	}
	
	public function getVerbe($class, $arrClass=false){

        //récupération du verbe
        if(!$arrClass) $arrClass = $this->getAleaClass($class);
        
		//ajoute le verbe
        $this->arrClass[$this->ordre]["verbe"] = $arrClass;
                
        //met à jour l'élision
        $this->arrClass[$this->ordre]["elision"] = $arrClass["elision"];
        
        return $arrClass;
	}
	
	public function getAleaClass($class){
		
        //cherche la définition de la class
        $arrCpt = $this->getClassDef($class);
        
        //enregistre le potentiel
        $this->potentiel += count($arrCpt["dst"]);
        
        //choisi un concept aléatoirement
        $a = rand(0, count($arrCpt["dst"])-1);
        
        $cpt = $arrCpt["dst"][$a];
        
        //Vérifie si le concept est un générateur
        if(isset($cpt["id_gen"])){
        	//vérivie s'il faut générer la forme
        	if(substr_count($cpt['valeur'], "[")>1){
        		//génére l'expression
				$m = new Gen_Moteur();
				$m->arrDicos = $this->arrDicos;		
				//génére la classe
				$m->Generation($cpt['valeur'],false);
				//récupère les class générée
				$cpt = $m;
				$this->potentiel += $m->potentiel;
        	}else{
        		$this->traiteClass($cpt['valeur']);
        	}
		}
		
		return $cpt; 			
		
	}
	
	public function getClassDef($class){

        //récupère la définition de la class
		$arrClass=explode("_", $class);
        $table = new Model_DbTable_Concepts();
        $arrCpt = $table->obtenirConceptDescription($this->arrDicos['concepts'],$arrClass);
		
        return $arrCpt;
	}

	
	
	public function TraiteAction($chaine, $action){

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
										$this->TraiteAction($frag, $act->action);					
									}else{
										$this->TraiteAction($frag, $act->NoVerifAction);					
									}
									break;								
								case "VerifDeb":
									if($i==0){
										$this->TraiteAction($frag, $act->action);					
									}else{
										$this->TraiteAction($frag, $act->NoVerifAction);					
									}
									break;								
								default:
									$this->TraiteAction($frag, $act);								
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
					$this->TraiteAction($chaine, $action->action);					
				}else{
					$this->TraiteAction($chaine, $action->NoVerifAction);					
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
	    $style = "border: {$bordure}px solid black;"; // les accolades permettent de coller la valeur numérique à "px"
	
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
		    $aafficher .= "<td style='$style'>$valeur</td>\n</tr>\n";
	    }
	    
	    /* on ferme le tableau HTML (nécessaire pour la validité) */
	    $aafficher .= "</table>\n";
	    
	    return $aafficher;
	}
	
	
}
?>