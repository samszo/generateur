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
 * @package    Dico
 * @copyright  Copyright (c) 2010 J-P Balpe (http://www.balpe.com)
 * @license    http://framework.generateur.com/license/ Artistic/GPL License
 * @version    $Id: Dico.php 0 2010-09-20 15:26:09Z samszo $
 */

/**
 * Concrete class for generating Fragment for Generateur.
 *
 * @category   Generateur
 * @package    Dico
 * @copyright  Copyright (c) 2010 J-P Balpe (http://www.balpe.com)
 * @license    http://framework.generateur.com/license/ Artistic/GPL License
 */

class Gen_Dico
{

	/**
	 * @xml le dictionnaire au format xml
	 */
	var $xml;
	var $xmlRoot;
	var $xmlTmp;
	var $xmlDesc;
	var $url;
	var $urlS;
	var $pathS;
	var $type;
	var $Xtype;
	var $id;

	/**
	 * Le constructeur initialise le dictionnaire.
	 */
	public function __construct($urlDesc="") {

		if($urlDesc=="")$urlDesc=APPLICATION_PATH.'/configs/LangageDescripteur.xml';
		$this->urlDesc = $urlDesc;
		
			
	}

	public function GetMacToXml($idDico){

		//récupération des infos du dico
		$this->id = $idDico;
		$dbDico = new Model_DbTable_Dicos();		
		$arr = $dbDico->obtenirDico($this->id);
		$this->urlS = $arr['url_source'];
		$this->type = $arr['type'];
		$this->Xtype = $this->RemoveAccents($this->type);
		$this->pathS = $arr['path_source'];
		
		//chargement de la configuration du langage
		$this->xmlDesc = simplexml_load_file($this->urlDesc);

		//initialisation du xml de stockage du dico
		$this->InitXmlDico();
		
		//chargement du dictionnaire
		$chaines = file($this->pathS);
		
		//chargement du traitement défini dans la description du langage
		$trtmt = $this->xmlDesc->xpath("//dico[@type='".$this->type."']/traitements");		
		
		// parcourt toute les ligne du dictionnaire
		foreach ($chaines as $line_num => $chaine) {
			//applique le traitement pour chaque ligne
			foreach ($trtmt[0]->children() as $action) {
				//pour faciliter la gestion des explode
				$chaine = str_replace("\r","- -",$chaine);
				//supprime les retours chariots en trop
				$chaine = str_replace("- -- -- -","- -- -",$chaine);
				
				//pour l'encode des caractère
				//$chaine = mb_convert_encoding($chaine, "UTF-8", "macintosh");
				//$chaine = iconv('UTF-8', 'macintosh////IGNORE', $chaine);
				//$chaine = htmlentities($chaine);
				//$chaine = html_entity_decode($chaine); 
				//$chaine = utf8_encode($chaine);
				//http://sebastienguillon.com/journal/2006/03/convertir-macroman-en-utf-8
				// en C : http://alienryderflex.com/utf-8/
				$chaine = $this->MacRoman_to_utf8($chaine);
				
				$this->TraiteAction($chaine, $action);
			}
		}
		$this->Save();
	}

	public function Save(){

		$date = new Zend_Date();
		
		if($this->xml){
			$this->xml->save(ROOT_PATH."/data/dicos/".$this->Xtype."_".$date->getTimestamp().".xml");
			$this->url = WEB_ROOT."/data/dicos/".$this->Xtype."_".$date->getTimestamp().".xml";
		}else{
			$this->url = "";
		}
			
		//création des objects de bd
		$dbDico = new Model_DbTable_Dicos();
		
		//enregistre le dico
		if($this->id){
			$dbDico->modifierDico($this->id,$this->url, $this->type, $this->urlS);
		}else{
			$pk = $dbDico->ajouterDico($this->url, $this->type, $this->urlS, $this->pathS);
			$this->id = $pk;
		}					
       
		
	}

	public function SaveBdd($idDico){
		
		
		//récupère les infos du dico
		$dbDico = new Model_DbTable_Dicos();
		$this->id = $idDico;
		$arrD = $dbDico->obtenirDico($this->id);		
		
		//parcourt le xml
		$path = str_replace(WEB_ROOT,ROOT_PATH,$arrD['url']);
		$this->xml = simplexml_load_file($path);

		//création des objets de base
		switch ($arrD['type']) {
			case 'conjugaisons':
				$dbConj = new Model_DbTable_Conjugaisons();
				$dbTrm = new Model_DbTable_Terminaisons();
				break;
			case 'déterminants':
				$dbDtm = new Model_DbTable_Determinants();
				break;
			case 'compléments':
				$dbCpm = new Model_DbTable_Complements();
				break;
			case 'syntagmes':
				$dbSyn = new Model_DbTable_Syntagmes();
				break;
			case 'concepts':
				$dbCon = new Model_DbTable_Concepts();
				$dbVer = new Model_DbTable_Verbes();
				$dbAdj = new Model_DbTable_Adjectifs();
				$dbSub = new Model_DbTable_Substantifs();
				$dbSyn = new Model_DbTable_Syntagmes();
				$dbGen = new Model_DbTable_Generateurs();
				$dbConVer = new Model_DbTable_ConceptsVerbes();
				$dbConAdj = new Model_DbTable_ConceptsAdjectifs();
				$dbConSub = new Model_DbTable_ConceptsSubstantifs();
				$dbConSyn = new Model_DbTable_ConceptsSyntagmes();
				$dbConGen = new Model_DbTable_ConceptsGenerateurs();
				break;
		}
		
		foreach ($this->xml->children() as $k => $n) {
			switch ($k) {
				case 'conjugaison':
					$pkV = $dbConj->ajouterConjugaison($this->id,$n['num'],$n['modele']);
					$i=0;
					foreach ($n->terminaisons->terminaison as $kTrm => $nTrm) {
						$dbTrm->ajouterTerminaison($pkV,$i,$nTrm);
						$i++;
					}
					break;
				case 'determinants':
					$num = $n['num'];
					$i=0;
					foreach ($n->determinant as $kDtm => $nDtm) {
						$dbDtm->ajouterDeterminant($this->id,$num,$i,$nDtm);
						$i++;
					}
					break;
				case 'complements':
					$num = $n['num'];
					$i=0;
					foreach ($n->complement as $kCpm => $nCpm) {
						$dbCpm->ajouterComplement($this->id,$num,$i,$nCpm);
						$i++;
					}
					break;
				case 'syntagmes':
					$num = $n['num'];
					$i=0;
					foreach ($n->syntagme as $kSyn => $nSyn) {
						$dbSyn->ajouterSyntagme($this->id,$num,$i,$nSyn);
						$i++;
					}
					break;
				case 'concept':
					$idC = $dbCon->ajouterConcept($this->id,$n['lib'],$n['type']);						
					foreach ($n->children() as $kCon => $nCon) {
						//ajout suivant le type de concept
				    	switch ($kCon) {
				    		case 'verbe':
				    			$idT = $dbVer->ajouterVerbe($this->id,-1,$nCon["eli"],$nCon["pref"],$nCon["modele"]);
				    			$dbConVer->ajouterConceptVerbe($idC,$idT);
				    			break;				    		
				    		case 'adj':
				    			$idT = $dbAdj->ajouterAdjectif($this->id,$nCon["eli"],$nCon["pref"],$nCon["ms"],$nCon["fs"],$nCon["mp"],$nCon["fp"]);
				    			$dbConAdj->ajouterConceptAdjectif($idC,$idT);
				    			break;				    		
				    		case 'syn':
				    			$idT = $dbSyn->ajouterSyntagme($this->id,-1,-1,$nCon);
				    			$dbConSyn->ajouterConceptSyntagme($idC,$idT);
				    			break;				    		
				    		case 'sub':
				    			$idT = $dbSub->ajouterSubstantif($this->id,$nCon["eli"],$nCon["pref"],$nCon["s"],$nCon["p"]);
				    			$dbConSub->ajouterConceptSubstantif($idC,$idT);
				    			break;				    		
				    		case 'gen':
				    			$idT = $dbGen->ajouterGenerateur($this->id,$nCon);
				    			$dbConGen->ajouterConceptGenerateur($idC,$idT);
				    			break;				    		
				    	}
					}
					break;
			}
		}
		
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

			case 'SetNoeud':
				$this->SetNoeud($chaine, $action);
				break;
								
			case 'SetConjugaison':
				$this->SetConjugaison($chaine, $action);
				break;
				
			case 'SetModele':
				$this->SetModele($chaine, $action);
				break;
			
			case 'SetConcept':
				$this->SetConcept($chaine, $action);
				break;
				
			
			case 'SetTerminaison':
				$this->SetTerminaison($chaine, $action);
				break;
		}

	}
        	
	public function SetConjugaison($chaine, $action){
		
		//calcul les attributs
		$c = $action['char']."";
		$n = strpos($chaine, $c);
		$num = substr($chaine,1,strpos($chaine, $c)-1);
		$verbe = substr($chaine,strpos($chaine, $c)+1,-1);
		//création du noeud verbe
		$xFrag = $this->xml->createElement('conjugaison',$chaine);
		$xAtt = $this->xml->createAttribute('num');
		$nText = $this->xml->createTextNode($num);
		$xAtt->appendChild($nText); 				
		$xFrag->appendChild($xAtt);
		$xAtt = $this->xml->createAttribute('modele');
		$nText = $this->xml->createTextNode($verbe);
		$xAtt->appendChild($nText);
		$xFrag->appendChild($xAtt);
		//ajoute les terminaisons au verbe
		$xFrag->appendChild($this->xmlTmp);
		$this->xmlTmp = false;
		//ajoute le verbe à la racine		
		$this->xmlRoot->appendChild($xFrag);
		
	}
	
	public function SetTerminaison($chaine, $action){

		if(!$this->xmlTmp){
			//initialise le noeud
			$this->xmlTmp = $this->xml->createElement("terminaisons");			
		}
		//calcul le noeud
		$n = $this->xml->createElement("terminaison",$chaine);		
		//ajoute le noeud
		$this->xmlTmp->appendChild($n);		
				
	}	
	
	public function SetNoeud($chaine, $action){

		if(!$this->xmlTmp){
			//initialise le noeud
			$this->xmlTmp = $this->xml->createElement($this->Xtype);			
		}
		//calcul le noeud
		if($this->type=="concepts") {
			$this->SetTypeConcept($this->xmlTmp->getAttribute('type'), $chaine);			
		}else{
			$n = $this->xml->createElement(substr($this->Xtype,0,-1),$chaine);		
			//ajoute le noeud
			$this->xmlTmp->appendChild($n);		
		}
				
	}

	public function SetTypeConcept($type, $chaine){
				
		if(strpos($chaine,"[")===false){
			switch ($type) {
				case "a":
					//création d'un adjectif
					$this->SetAdjectif($chaine);		
					break;
				case "m":
					//création d'un substantif
					$this->SetSubstantif($chaine);		
					break;
				case "s":
					//création d'un substantif
					$this->SetSyntagme($chaine);		
					break;
				case "v":
					//création d'un substantif
					$this->SetVerbe($chaine);		
					break;
			}
		}else{
			//création d'un lien vers un concept
			$this->SetGen($chaine);		
		}
						
	}


	public function SetVerbe($chaine){
		

		//calcul les attributs
		$elision = substr($chaine,0,1);		
		$arr = explode("|",substr($chaine,2));
		$prefix =  $arr[0];
		$modele = $arr[1];

		//création du noeud verbe
		$xFrag = $this->xml->createElement('verbe');
    
		//création des attributs
		$xAtt = $this->xml->createAttribute('eli');
		$nText = $this->xml->createTextNode($elision);
		$xAtt->appendChild($nText); 				
		$xFrag->appendChild($xAtt);
		
		$xAtt = $this->xml->createAttribute('pref');
		$nText = $this->xml->createTextNode($prefix);
		$xAtt->appendChild($nText); 				
		$xFrag->appendChild($xAtt);
		
		$xAtt = $this->xml->createAttribute('modele');
		$nText = $this->xml->createTextNode($modele);
		$xAtt->appendChild($nText);
		$xFrag->appendChild($xAtt);

		$this->xmlTmp->appendChild($xFrag);		
		
	}
	
	
	public function SetSubstantif($chaine){
		

		//calcul les attributs
		$elision = substr($chaine,0,1);
		$genre = substr($chaine,2,1);
		$c = substr($chaine,4);
		$arr = explode("|",$c);
		$prefix =  $arr[0];
		
		if(count($arr)==1){
			$arr[1] = "";
		}
		
		$accords = explode(" ",$arr[1]);
		if(count($accords)==2){
			$s = $accords[0];
			$p = $accords[1];
		}else{
			$s = "";
			$p = $accords[0];
		}
		
		//création du noeud substantif
		$xFrag = $this->xml->createElement('sub');
    
		//création des attributs
		$xAtt = $this->xml->createAttribute('eli');
		$nText = $this->xml->createTextNode($elision);
		$xAtt->appendChild($nText); 				
		$xFrag->appendChild($xAtt);
		
		$xAtt = $this->xml->createAttribute('genre');
		$nText = $this->xml->createTextNode($genre);
		$xAtt->appendChild($nText); 				
		$xFrag->appendChild($xAtt);

		$xAtt = $this->xml->createAttribute('pref');
		$nText = $this->xml->createTextNode($prefix);
		$xAtt->appendChild($nText); 				
		$xFrag->appendChild($xAtt);
		
		$xAtt = $this->xml->createAttribute('s');
		$nText = $this->xml->createTextNode($s);
		$xAtt->appendChild($nText);
		$xFrag->appendChild($xAtt);

		$xAtt = $this->xml->createAttribute('p');
		$nText = $this->xml->createTextNode($p);
		$xAtt->appendChild($nText);
		$xFrag->appendChild($xAtt);

		$this->xmlTmp->appendChild($xFrag);		
		
	}
	
	public function SetAdjectif($chaine){
		

		//calcul les attributs
		$elision = substr($chaine,0,1);
		$c = substr($chaine,strpos($chaine, ")")+1);
		$arr = explode("|",$c);
		$prefix =  $arr[0];
		$accords = explode(" ",$arr[1]);
		$m_s = $accords[0];
		$f_s = $accords[1];
		$m_p = $accords[2];
		$f_p = $accords[3];
		
		//création du noeud adjectif
		$xFrag = $this->xml->createElement('adj');
		
		//création des attributs
		$xAtt = $this->xml->createAttribute('eli');
		$nText = $this->xml->createTextNode($elision);
		$xAtt->appendChild($nText); 				
		$xFrag->appendChild($xAtt);
		
		$xAtt = $this->xml->createAttribute('pref');
		$nText = $this->xml->createTextNode($prefix);
		$xAtt->appendChild($nText);
		$xFrag->appendChild($xAtt);

		$xAtt = $this->xml->createAttribute('ms');
		$nText = $this->xml->createTextNode($m_s);
		$xAtt->appendChild($nText);
		$xFrag->appendChild($xAtt);

		$xAtt = $this->xml->createAttribute('fs');
		$nText = $this->xml->createTextNode($f_s);
		$xAtt->appendChild($nText);
		$xFrag->appendChild($xAtt);

		$xAtt = $this->xml->createAttribute('mp');
		$nText = $this->xml->createTextNode($m_p);
		$xAtt->appendChild($nText);
		$xFrag->appendChild($xAtt);

		$xAtt = $this->xml->createAttribute('fp');
		$nText = $this->xml->createTextNode($f_p);
		$xAtt->appendChild($nText);
		$xFrag->appendChild($xAtt);
		
		$this->xmlTmp->appendChild($xFrag);		
				
	}
	
	public function SetSyntagme($chaine){
		
		
		//création du noeud adjectif
		$xFrag = $this->xml->createElement('syn',$chaine);
		
		$this->xmlTmp->appendChild($xFrag);		
				
	}
	
	public function SetGen($chaine){
		
		$xFrag = $this->xml->createElement('gen');
		$cm = $this->xml->createTextNode($chaine);
		$ct = $this->xml->createCDATASection($chaine);
		$xFrag->appendChild($ct);

		$this->xmlTmp->appendChild($xFrag);		
	}
		
	
	public function SetModele($chaine, $action){
		
		if(!$this->xmlTmp){
			//initialise le noeud
			$this->xmlTmp = $this->xml->createElement($this->Xtype);			
		}
		//création de l'attribut 
		$xAtt = $this->xml->createAttribute('num');
		$nText = $this->xml->createTextNode($chaine);
		$xAtt->appendChild($nText); 				
		$this->xmlTmp->appendChild($xAtt);
		//ajoute le modèle à la racine		
		$this->xmlRoot->appendChild($this->xmlTmp);
		//réiniitialise l'xml temporaire
		$this->xmlTmp = false;
		
	}
		
	public function SetConcept($chaine, $action){
		
		//initialise l'xml temporaire
		$this->xmlTmp = $this->xml->createElement(substr($this->Xtype,0,-1));			
		
		//création de l'attribut type
		$xAtt = $this->xml->createAttribute('type');
		$type = substr($chaine,0,strpos($chaine, "_"));
		$nText = $this->xml->createTextNode($type);
		$xAtt->appendChild($nText); 				
		$this->xmlTmp->appendChild($xAtt);
		
		//création de l'attribut lib
		$xAtt = $this->xml->createAttribute('lib');
		$lib = substr($chaine,strpos($chaine, "_")+1);
		$nText = $this->xml->createTextNode($lib);
		$xAtt->appendChild($nText); 				
		$this->xmlTmp->appendChild($xAtt);
		
		//ajoute le modèle à la racine		
		$this->xmlRoot->appendChild($this->xmlTmp);
		
	}
	
	public function InitXmlDico(){
		//initialisation du fichier xml
		$this->xml = new DOMDocument('1.0', 'UTF-8');
		$this->xml->appendChild(new DOMComment('dictionnaire '.$this->type.' pour le générateur de texte'));
		$this->xmlRoot = $this->xml->appendChild(new DOMElement('dico'));
		$xAtt = $this->xml->createAttribute('type');
		$this->xmlRoot->appendChild($xAtt);
		$nText = $this->xml->createTextNode($this->type);
		$xAtt->appendChild($nText); 				
		$xAtt = $this->xml->createAttribute('urlS');
		$this->xmlRoot->appendChild($xAtt);
		$nText = $this->xml->createTextNode($this->urlS);
		$xAtt->appendChild($nText); 				
	}
	
	public function AjoutFrag($frag, $action){
		//création du noeud xml
		$xFrag = $this->xml->createElement('frag',$frag);
		$dom_sxe = dom_import_simplexml($action);
		$dom_sxe = $this->xml->importNode($dom_sxe, true);
		$this->xmlRoot->appendChild($dom_sxe);		
		$this->xmlRoot->appendChild($xFrag);		
	}


	function MacRoman_to_utf8($str, $break_ligatures='none')
	{
		// $break_ligatures : 'none' | 'fifl' | 'all'
		// 'none' : don't break any MacRoman ligatures, transform them into their utf-8 counterparts
		// 'fifl' : break only fi ("\xDE" => "fi") and fl ("\xDF"=>"fl")
		// 'all' : break fi, fl and also AE ("\xAE"=>"AE"), ae ("\xBE"=>"ae"), OE ("\xCE"=>"OE") and oe ("\xCF"=>"oe")
	
		if($break_ligatures == 'fifl')
		{
			$str = strtr($str, array("\xDE"=>"fi", "\xDF"=>"fl"));
		}
		
		if($break_ligatures == 'all')
		{
			$str = strtr($str, array("\xDE"=>"fi", "\xDF"=>"fl", "\xAE"=>"AE", "\xBE"=>"ae", "\xCE"=>"OE", "\xCF"=>"oe"));
		}
	
		$str = strtr($str, array("\x7F"=>"\x20", "\x80"=>"\xC3\x84", "\x81"=>"\xC3\x85",
		"\x82"=>"\xC3\x87", "\x83"=>"\xC3\x89", "\x84"=>"\xC3\x91", "\x85"=>"\xC3\x96",
		"\x86"=>"\xC3\x9C", "\x87"=>"\xC3\xA1", "\x88"=>"\xC3\xA0", "\x89"=>"\xC3\xA2",
		"\x8A"=>"\xC3\xA4", "\x8B"=>"\xC3\xA3", "\x8C"=>"\xC3\xA5", "\x8D"=>"\xC3\xA7",
		"\x8E"=>"\xC3\xA9", "\x8F"=>"\xC3\xA8", "\x90"=>"\xC3\xAA", "\x91"=>"\xC3\xAB",
		"\x92"=>"\xC3\xAD", "\x93"=>"\xC3\xAC", "\x94"=>"\xC3\xAE", "\x95"=>"\xC3\xAF",
		"\x96"=>"\xC3\xB1", "\x97"=>"\xC3\xB3", "\x98"=>"\xC3\xB2", "\x99"=>"\xC3\xB4",
		"\x9A"=>"\xC3\xB6", "\x9B"=>"\xC3\xB5", "\x9C"=>"\xC3\xBA", "\x9D"=>"\xC3\xB9",
		"\x9E"=>"\xC3\xBB", "\x9F"=>"\xC3\xBC", "\xA0"=>"\xE2\x80\xA0", "\xA1"=>"\xC2\xB0",
		"\xA2"=>"\xC2\xA2", "\xA3"=>"\xC2\xA3", "\xA4"=>"\xC2\xA7", "\xA5"=>"\xE2\x80\xA2",
		"\xA6"=>"\xC2\xB6", "\xA7"=>"\xC3\x9F", "\xA8"=>"\xC2\xAE", "\xA9"=>"\xC2\xA9",
		"\xAA"=>"\xE2\x84\xA2", "\xAB"=>"\xC2\xB4", "\xAC"=>"\xC2\xA8", "\xAD"=>"\xE2\x89\xA0",
		"\xAE"=>"\xC3\x86", "\xAF"=>"\xC3\x98", "\xB0"=>"\xE2\x88\x9E", "\xB1"=>"\xC2\xB1",
		"\xB2"=>"\xE2\x89\xA4", "\xB3"=>"\xE2\x89\xA5", "\xB4"=>"\xC2\xA5", "\xB5"=>"\xC2\xB5",
		"\xB6"=>"\xE2\x88\x82", "\xB7"=>"\xE2\x88\x91", "\xB8"=>"\xE2\x88\x8F", "\xB9"=>"\xCF\x80",
		"\xBA"=>"\xE2\x88\xAB", "\xBB"=>"\xC2\xAA", "\xBC"=>"\xC2\xBA", "\xBD"=>"\xCE\xA9",
		"\xBE"=>"\xE6", "\xBF"=>"\xC3\xB8", "\xC0"=>"\xC2\xBF", "\xC1"=>"\xC2\xA1",
		"\xC2"=>"\xC2\xAC", "\xC3"=>"\xE2\x88\x9A", "\xC4"=>"\xC6\x92", "\xC5"=>"\xE2\x89\x88",
		"\xC6"=>"\xE2\x88\x86", "\xC7"=>"\xC2\xAB", "\xC8"=>"\xC2\xBB", "\xC9"=>"\xE2\x80\xA6",
		"\xCA"=>"\xC2\xA0", "\xCB"=>"\xC3\x80", "\xCC"=>"\xC3\x83", "\xCD"=>"\xC3\x95",
		"\xCE"=>"\xC5\x92", "\xCF"=>"\xC5\x93", "\xD0"=>"\xE2\x80\x93", "\xD1"=>"\xE2\x80\x94",
		"\xD2"=>"\xE2\x80\x9C", "\xD3"=>"\xE2\x80\x9D", "\xD4"=>"\xE2\x80\x98", "\xD5"=>"\xE2\x80\x99",
		"\xD6"=>"\xC3\xB7", "\xD7"=>"\xE2\x97\x8A", "\xD8"=>"\xC3\xBF", "\xD9"=>"\xC5\xB8",
		"\xDA"=>"\xE2\x81\x84", "\xDB"=>"\xE2\x82\xAC", "\xDC"=>"\xE2\x80\xB9", "\xDD"=>"\xE2\x80\xBA",
		"\xDE"=>"\xEF\xAC\x81", "\xDF"=>"\xEF\xAC\x82", "\xE0"=>"\xE2\x80\xA1", "\xE1"=>"\xC2\xB7",
		"\xE2"=>"\xE2\x80\x9A", "\xE3"=>"\xE2\x80\x9E", "\xE4"=>"\xE2\x80\xB0", "\xE5"=>"\xC3\x82",
		"\xE6"=>"\xC3\x8A", "\xE7"=>"\xC3\x81", "\xE8"=>"\xC3\x8B", "\xE9"=>"\xC3\x88",
		"\xEA"=>"\xC3\x8D", "\xEB"=>"\xC3\x8E", "\xEC"=>"\xC3\x8F", "\xED"=>"\xC3\x8C",
		"\xEE"=>"\xC3\x93", "\xEF"=>"\xC3\x94", "\xF0"=>"\xEF\xA3\xBF", "\xF1"=>"\xC3\x92",
		"\xF2"=>"\xC3\x9A", "\xF3"=>"\xC3\x9B", "\xF4"=>"\xC3\x99", "\xF5"=>"\xC4\xB1",
		"\xF6"=>"\xCB\x86", "\xF7"=>"\xCB\x9C", "\xF8"=>"\xC2\xAF", "\xF9"=>"\xCB\x98",
		"\xFA"=>"\xCB\x99", "\xFB"=>"\xCB\x9A", "\xFC"=>"\xC2\xB8", "\xFD"=>"\xCB\x9D",
		"\xFE"=>"\xCB\x9B", "\xFF"=>"\xCB\x87", "\x00"=>"\x20", "\x01"=>"\x20",
		"\x02"=>"\x20", "\x03"=>"\x20", "\x04"=>"\x20", "\x05"=>"\x20",
		"\x06"=>"\x20", "\x07"=>"\x20", "\x08"=>"\x20", "\x0B"=>"\x20",
		"\x0C"=>"\x20", "\x0E"=>"\x20", "\x0F"=>"\x20", "\x10"=>"\x20",
		"\x11"=>"\x20", "\x12"=>"\x20", "\x13"=>"\x20", "\x14"=>"\x20",
		"\x15"=>"\x20", "\x16"=>"\x20", "\x17"=>"\x20", "\x18"=>"\x20",
		"\x19"=>"\x20", "\x1A"=>"\x20", "\x1B"=>"\x20", "\x1C"=>"\x20",
		"\1D"=>"\x20", "\x1E"=>"\x20", "\x1F"=>"\x20", "\xF0"=>""));
		
		return $str;
	}
	
	function RemoveAccents($str, $charset='utf-8')
	{
	    $str = htmlentities($str, ENT_NOQUOTES, $charset);
	    
	    $str = preg_replace('#\&([A-za-z])(?:acute|cedil|circ|grave|ring|tilde|uml)\;#', '\1', $str);
	    $str = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $str); // pour les ligatures e.g. '&oelig;'
	    $str = preg_replace('#\&[^;]+\;#', '', $str); // supprime les autres caractères
	    
	    return $str;
	}
	
	
}
?>