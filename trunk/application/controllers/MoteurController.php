<?php
class MoteurController extends Zend_Controller_Action
{

    public function moteurAction()
    {
    }
    
    public function testerAction()
    {
    try{
    	
        $form = new Form_Moteur();
	    $form->envoyer->setLabel('Tester');
	    $this->view->form = $form;

	    if ($this->getRequest()->isPost()) {
	        $formData = $this->getRequest()->getPost();
	        if ($form->isValid($formData)) {
	        	//calcul l'expresion saisie
				$moteur = new Gen_Moteur();
				$arrDicos = array(
					"concepts"=>17
					,"syntagmes"=>4
					,"pronoms_complement"=>13
					,"conjugaisons"=>11
					,"pronoms"=>14
					,"déterminants"=>15
					,"negations"=>16		
					);		
				$moteur->arrDicos = $arrDicos;		
				$moteur->Generation($form->getValue('valeur'));
	        		        	
				$this->view->Generation = $moteur->texte;
	        }
	    }
	    
    }catch (Zend_Exception $e) {
          // Appeler Zend_Loader::loadClass() sur une classe non-existante
          //entrainera la levée d'une exception dans Zend_Loader
          echo "Récupère exception: " . get_class($e) . "\n";
          echo "Message: " . $e->getMessage() . "\n";
          // puis tout le code nécessaire pour récupérer l'erreur
	}
	    
    }
    
}

