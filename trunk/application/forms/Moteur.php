<?php
class Form_Moteur extends Zend_Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->setName('moteur');

		$dbDico = new Model_DbTable_Dicos();
		$arrDicos = $dbDico->obtenirDicoType("concepts");

		$multiOptions = array();
		foreach ($arrDicos as $dico) {
		    $multiOptions[$dico["id_dico"]] = $dico["nom"];
		    $multiOptionsSelected[] = $dico["id_dico"];
		}

		$dicoCheckboxes = new Zend_Form_Element_MultiCheckbox('dicoIds', array(
		    'multiOptions' => $multiOptions
		));
		$dicoCheckboxes->setLabel('Définir les dictionnaires de concepts');
		
		$dicoCheckboxes->setValue($multiOptionsSelected);		        
        
      	$valeur = new Zend_Form_Element_Textarea('valeur');
      	$valeur->setRequired(true);
		$valeur->setLabel('Définir une valeur à générer');
      	
        $envoyer = new Zend_Form_Element_Submit('envoyer');
        $envoyer->setAttrib('id', 'boutonenvoyer');

        $this->setAttrib('enctype', 'multipart/form-data');
        $this->addElements(array($id, $valeur, $dicoCheckboxes, $envoyer));
    }
}
