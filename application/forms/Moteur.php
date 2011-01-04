<?php
class Form_Moteur extends Zend_Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->setName('moteur');
       
      	$valeur = new Zend_Form_Element_Textarea('valeur');
      	$valeur->setRequired(true);
		$valeur->setLabel('Définir une valeur');
      	
        $envoyer = new Zend_Form_Element_Submit('envoyer');
        $envoyer->setAttrib('id', 'boutonenvoyer');

        $this->setAttrib('enctype', 'multipart/form-data');
        $this->addElements(array($id, $valeur, $envoyer));
    }
}
