<?php

class Form_Verbe extends Zend_Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->setName('verbe');

        $id = new Zend_Form_Element_Hidden('id');
        $id->setValue($options["id"]);
        
        $CacheForm = new Zend_Form_Element_Hidden('CacheForm');
        $CacheForm->setValue(false);
        
		//construction des modèle de conjugaison
        $conj = new Zend_Form_Element_Select('id_conj', array(
		    'multiOptions' => $options["RsConjs"]));
		$conj->setRequired(true);
        $conj->setLabel('Choisir un modèle de conjugaison:');
		
      	$eli = new Zend_Form_Element_Select('elision', array(
		    'multiOptions' => array(1=>"avec",0=>"sans")));
      	$eli->setRequired(true);
		$eli->setLabel("Définir l'élision:");

      	$prefix = new Zend_Form_Element_Text('prefix');
      	$prefix->setRequired(true);
		$prefix->setLabel('Définir un prefix:');
		
        $envoyer = new Zend_Form_Element_Submit('envoyer');
        $envoyer->setAttrib('id', 'boutonenvoyer');
        
		$this->setAttrib('enctype', 'multipart/form-data');
        $this->addElements(array($id, $CacheForm, $conj, $eli, $prefix, $envoyer));
    }
}
