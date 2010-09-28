<?php

class Form_Verbe extends Zend_Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->setName('verbe');

        $id = new Zend_Form_Element_Hidden('id');
        $id->setValue($options["id"]);

      	$model = new Zend_Form_Element_Text('model');
      	$model->setRequired(true);
		$model->setLabel('Définir un modèle');
      	
      	$num = new Zend_Form_Element_Text('num');
      	$num->setRequired(true);
		$num->setLabel('Définir un numéro');
      	
        $envoyer = new Zend_Form_Element_Submit('envoyer');
        $envoyer->setAttrib('id', 'boutonenvoyer');
		$this->setAttrib('enctype', 'multipart/form-data');
        $this->addElements(array($id, $model, $num, $envoyer));
    }
}
