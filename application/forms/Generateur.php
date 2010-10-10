<?php
class Form_Generateur extends Zend_Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->setName('generateur');

        $id = new Zend_Form_Element_Hidden('id');
        $id->setValue($options["id"]);
        
      	$valeur = new Zend_Form_Element_Text('valeur');
      	$valeur->setRequired(true);
		$valeur->setLabel('Définir une valeur');
      	
        $envoyer = new Zend_Form_Element_Submit('envoyer');
        $envoyer->setAttrib('id', 'boutonenvoyer');
		$this->setAttrib('enctype', 'multipart/form-data');
        $this->addElements(array($id, $valeur, $envoyer));
    }
}
