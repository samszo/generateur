<?php

class Form_Verbe extends Zend_Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->setName('verbe');

        $id = new Zend_Form_Element_Hidden('id');
        $id->setValue($options["id"]);
      	        
		//construction des modèle de conjugaison
		$conjs = new Model_DbTable_Conjugaisons();
		$arrV = $conjs->fetchAll();		               
        $modele = new Zend_Form_Element_Select('modele', array(
		    'multiOptions' => $arrV));
        $modele->setLabel('Choisir un modèle de conjugaison');
		$modele->setRequired(true);
		
      	$eli = new Zend_Form_Element_Select('elision', array(
		    'multiOptions' => array(1=>"avec",0=>"sans")));
      	$eli->setRequired(true);
		$eli->setLabel("Définir l'élision");

      	$prefix = new Zend_Form_Element_Text('prefix');
      	$prefix->setRequired(true);
		$prefix->setLabel('Définir un prefix');
		
        $envoyer = new Zend_Form_Element_Submit('envoyer');
        $envoyer->setAttrib('id', 'boutonenvoyer');
		$this->setAttrib('enctype', 'multipart/form-data');
        $this->addElements(array($id, $modele, $eli, $prefix, $envoyer));
    }
}
