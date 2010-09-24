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
     * @type tableau des types de dictionnaire
     */
	var $types = array("général","conjugaison","déterminant","syntagme","complément","oeuvre");
    /**
     * @xml le dictionnaire au format xml
     */
	var $xml = "";
	var $url = "";
	
	/**
	 * Le constructeur initialise le dictionnaire. 
	 */
	public function __construct($url) {
		
		$this->url = $url;
		echo $this->url;
		
		//chargement du dictionnaire
		$lines = file($url);
		
		// Affiche toutes les lignes du tableau comme code HTML, avec les numéros de ligne
		foreach ($lines as $line_num => $line) {
			$arrFrag = split("/",$line);
			//récupération de chaque modèle de conjugaison
			foreach ($arrConj as $Conj) {
				//récupération de chaque instance de terminaison
				$arrFrag = split(",",$line);
				foreach ($arrFrag as $Frag) {
					//vérifie si on arrive à la déterminaison du fragment
					$c = substr($Frag,0,1);
					if($c=="("){
						$d = substr($Frag,0,-1);
						$arrD = split(".",$d);
						$num = $arrD[0];
						$verbe = $arrD[1];
					}
				}
			}
			echo "Line #<b>{$line_num}</b> : " . htmlspecialchars($line) . "<br />\n";
		}
		
	}

	
}
?>