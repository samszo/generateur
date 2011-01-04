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
	
	/**
	 * Le constructeur initialise le moteur.
	 * 	 */
	public function __construct($urlDesc="") {

		if($urlDesc=="")$urlDesc=APPLICATION_PATH.'/configs/LangageDescripteur.xml';
		$this->xmlDesc = simplexml_load_file($urlDesc);	
			
	}

	public function Generation($texte){
		
		$this->arrClass = array();
		$this->ordre = 0;
		
		//parcourt l'ensemble de la chaine
		for($i = 0; $i < strlen($texte); $i++)
        {
        	$c = $texte[$i];
        	//est-ce le début d'une classe
        	if($c == "["){
        		//on récupère la valeur de la classe et la position des caractères dans la chaine
        		$arrClass = $this->getClassVals(substr($texte,$i));
        		$this->class = $arrClass["valeur"];        		
        		
        		//on boucle sur les class
        		foreach($arrClass["arr"] as $cls){
        				$this->getClass($cls);
        		}
        			        		
        		//on passe à la chaine suivante
	        	$i=$arrClass["fin"];
        		$this->ordre ++;
        	}else{
        		//on ajoute le caractère
				$this->arrClass[$this->ordre]["texte"] .= $c;
        	}
        }
                
        //on calcule le texte
        $this->calculTexte();

	}
	
	public function getVecteur($arr){

		
		if(isset($arr["determinant"])){												
			//calcul le pluriel
			if($arr["determinant"]["pluriel"]){
				$txt = $arr["substantif"]["p"];
			}
		}
		
		
		if(isset($arr["substantif"])){			
			//calcul le détaerminant
			if($arr["substantif"]["elision"]==0 && $arr["substantif"]["genre"]==1){
				$det = $arr["determinant"]["vals"][0]["lib"]." ";
			}
			if($arr["substantif"]["elision"]==0 && $arr["substantif"]["genre"]==2){
				$det = $arr["determinant"]["vals"][1]["lib"]." ";
			}
			if($arr["substantif"]["elision"]==1 && $arr["substantif"]["genre"]==1){
				$det = $arr["determinant"]["vals"][2]["lib"];
			}
			if($arr["substantif"]["elision"]==1 && $arr["substantif"]["genre"]==2){
				$det = $arr["determinant"]["vals"][3]["lib"];
			}
		}
		
		return $det;
	}	
	
	public function genereTexte(){

		$this->texte = "";
		
		$this->ordre = 0;
		foreach($this->arrClass as $arr){
			if(isset($arr["texte"])){
				$this->texte .= $arr["texte"];
			}else{
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
						$adjs .= $this->genereAdjectif($arr);
					}
				}
				$this->texte .= $det.$sub." ".$adjs;					
			}
			$this->ordre ++;
		}
		
	}
	
	public function genereSubstantif($arr){

		$txt = $arr["substantif"]["s"];
		
		if(isset($arr["pluriel"])){												
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
					//récupération du substantif de référence
					$ordre = $this->ordre - $arr["determinant_verbe"][7];
					$sub = $this->arrClass[$ordre]["substantif"];			
					//récupère la génération du pronom
					$m = new Gen_Moteur();
					$m->arrDicos = $this->arrDicos;		
					//précise le genre
					$m->arrClass[0]["genre"]=$this->arrClass[$ordre]["genre"];
					//précise le nombre	
					$m->arrClass[0]["genre"]=$this->arrClass[$ordre]["pluriel"];	
					//génére la classe
					$m->Generation($arrP["lib"]);
					//récupère le pronom
					$arr["prosuj"] = $m->texte;
					if($this->arrClass[$ordre]["pluriel"])					
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
				//pronom par défault
				$arr["prosuj"] = $this->getPronom(3,"sujet");
				$arr["terminaison"] = 3;
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
	
	public function genereAdjectif($arr){

		$txt = "";

		//calcul le nombre
		$n = "_s"; 		
		if(isset($arr["pluriel"])){												
			$n = "_p";
		}
		
		//calcul le genre
		$g = "m"; 		
		if(isset($arr["genre"])){												
			if($arr["genre"]==2) $g = "f";
		}

		$txt = $arr["adjectif"]["prefix"].$arr["adjectif"][$g.$n];

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
		
		$num = ($temps*6)+$arr["terminaison"]; 		
		if($arr["determinant_verbe"][1]==8){
			$num= 36;
		}
		if($arr["determinant_verbe"][1]==9){
			$num= 37;
		}
		
		//calcul le numéro de la conjugaison
		if($arr["terminaison"]==7)
			

		$txt = $this->getTerminaison($arr["verbe"]["id_conj"],$num);

		return $txt;
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
		$arr["prodem"]="";
		$arr["prodem"]="";		
		
		//génère la négation
		if($arr["determinant_verbe"][0]!=0){
			$arr["finneg"] = $this->getNegation($arr["determinant_verbe"][0]);
			if($arr["elision"]==1){
				$arr["debneg"] = "n'";	
			}else{
				$arr["debneg"] = "ne ";
			}
		}
		
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
				if($eli==0){
					$verbe = $arr["debneg"]["lib"].$verbe; 
				}else{
					$verbe = $arr["debneg"]["lib_eli"].$verbe; 
				}
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
			$verbe = $arr["debneg"].$arr["prodem"].$verbe.$arr["prosuj"]["lib"].$arr["prodem"]["lib"];
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
				if($eli==0){
					$verbe = $arr["debneg"]["lib"].$verbe; 
				}else{
					$verbe = $arr["debneg"]["lib_eli"].$verbe; 
					$eli=0;
				}
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
				default:
					$cls = $this->getAleaClass($class);
					$this->arrClass[$this->ordre]["default"][] = $cls;
				break;
			}			
		}
		
		//vérifie si la class possède un blocage d'information
		$c = strpos($class,"#");
		if($c>0){
			$this->getSyntagme($class);	
		}

		//vérifie si la class possède un blocage d'information
		$c = strpos($class,"#");
		if($c>0){
			$this->getSyntagme($class);	
		}
		
	}
	
	public function getClassVals($class){

		$deb = strpos($class,"[");
		$fin = strpos($class,"]",substr($class, $deb+1));

		//on récupère la valeur de la classe
        $class = substr($class, $deb+1, $fin-1);
        
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

        $d = strpos($class,"∏");        
        if($d){
        	//récupère le substantif
	        $arr=explode("@", $class);        
        	$this->getSubstantif($arr[1]);
        	
        	//récupère les adjectifs
	        $arrAdj=explode("∏", $arr[0]);        
	        //récupère la définition des adjectifs
	        $arrClass = array();	       
	        foreach($arrAdj as $a){
		        $this->arrClass[$this->ordre]["adjectifs"][] = $this->getAleaClass($a);
	        	
	        }        	
        }else{
	        $this->arrClass[$this->ordre]["adjectifs"][] = $this->getAleaClass($class);        	
        }
	}
	
	public function getDeterminant($class){

        $arrClass = false;

        //vérifie si le déterminant est pour un verbe
        if(strlen($class) > 6){
        	//vérie si le determinant n'est pas transmis
        	$intD = intval($class);
        	$strD = $this->arrClass[$this->ordre]["determinant_verbe"];
        	if($intD==0){
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
        if($class > 100){
        	$pluriel = true;
        	$class = $class-100;
        }       			
        //vérifie s'il faut chercher le déterminant
        $arrClass = " ";
        if($dtr!=99){
	        $tDtr = new Model_DbTable_Determinants();
        	$arrClass = $tDtr->obtenirDeterminantByDicoNumNombre($this->arrDicos["déterminants"],$class,$pluriel);        				
        }
        //ajoute le déterminant
		$this->arrClass[$this->ordre]["determinant"] = $arrClass;
        
		//met à jour le nombre
		$this->arrClass[$this->ordre]["pluriel"] = $pluriel; 
                
        return $arrClass;
	}
	
	public function getSubstantif($class){

        //récupération du substantif
        $arrClass = $this->getAleaClass($class);
		//ajoute le substantif
        $this->arrClass[$this->ordre]["substantif"] = $arrClass;

        //met à jour le genre et l'élision
        $this->arrClass[$this->ordre]["genre"] = $arrClass;
        $this->arrClass[$this->ordre]["elision"] = $arrClass;
                
        return $arrClass;
	}

	public function getSyntagme($class){

        //récupère la définition de la class
        $table = new Model_DbTable_Syntagmes();
        $arrClass = $table->obtenirSyntagmeByDicoNum($this->arrDicos['syntagmes'],substr($class,1));
		
        $this->arrClass[$this->ordre]["syntagme"] = $arrClass;
        return $arr;

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

		return $arrP;										

	}

	public function getTerminaison($idConj, $num){

        //récupère la définition de la class
        $table = new Model_DbTable_Terminaisons();
        $arrClass = $table->obtenirConjugaisonByConjNum($idConj, $num);

		return $arrP["lib"];
												
	}
	
	public function getVerbe($class){

        //récupération du verbe
        $arrClass = $this->getAleaClass($class);
		//ajoute le verbe
        $this->arrClass[$this->ordre]["verbe"] = $arrClass;
                
        //met à jour l'élision
        $this->arrClass[$this->ordre]["elision"] = $arrClass;
        
        return $arrClass;
	}
	
	public function getAleaClass($class){
		
        //cherche la définition de la class
        $arrCpt = $this->getClassDef($class);
        
        //choisi un concept aléatoirement
        $a = rand(0, count($arrCpt["dst"])-1);
        
        $cpt = $arrCpt["dst"][$a];
        
        //Vérifie si le concept est un générateur
        if(isset($cpt["id_gen"])){
			//récupère la class
			$arrClass = $this->getClassVals($cpt['valeur']);
			//récupère le concept
			$cpt = $this->getAleaClass($arrClass["valeur"]);
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
	
}
?>