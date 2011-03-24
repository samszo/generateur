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

	    if ($this->getRequest()->isGet()) {
	        $formData = $this->getRequest()->getQuery();
	        if ($form->isValid($formData)) {
	        	//calcul l'expresion saisie
				$moteur = new Gen_Moteur("",false);
				$arrDicos = array(
					"concepts"=>implode(",", $form->getValue('dicoIdsCpt'))
					,"syntagmes"=>implode(",", $form->getValue('dicoIdsStg'))
					,"pronoms_complement"=>implode(",", $form->getValue('dicoIdsPrCp'))
					,"conjugaisons"=>implode(",", $form->getValue('dicoIdsCjg'))
					,"pronoms"=>implode(",", $form->getValue('dicoIdsPrSt')).",".implode(",", $form->getValue('dicoIdsPrCp'))
					,"déterminants"=>implode(",", $form->getValue('dicoIdsDtm'))
					,"negations"=>implode(",", $form->getValue('dicoIdsNgt'))		
					);
				//print_r($arrDicos);		
				$moteur->arrDicos = $arrDicos;		
				$moteur->Generation($form->getValue('valeur'));
	        		        	
				$this->view->Generation = $moteur->texte;
				$this->view->NbItem = $moteur->ordre;
				$this->view->Potentiel = $moteur->potentiel;
				$this->view->Detail = $moteur->detail;
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

    public function verifierAction()
    {
    try{
    	
        $form = new Form_Moteur();
	    $form->envoyer->setLabel('Verifier');
	    $this->view->form = $form;

	    if ($this->getRequest()->isGet()) {
	        $formData = $this->getRequest()->getQuery();
	        if ($form->isValid($formData)) {
	        	//calcul l'expresion saisie
				$moteur = new Gen_Moteur("",false);
				$arrDicos = array(
					"concepts"=>implode(",", $form->getValue('dicoIdsCpt'))
					,"syntagmes"=>implode(",", $form->getValue('dicoIdsStg'))
					,"pronoms_complement"=>implode(",", $form->getValue('dicoIdsPrCp'))
					,"conjugaisons"=>implode(",", $form->getValue('dicoIdsCjg'))
					,"pronoms"=>implode(",", $form->getValue('dicoIdsPrSt')).",".implode(",", $form->getValue('dicoIdsPrCp'))
					,"déterminants"=>implode(",", $form->getValue('dicoIdsDtm'))
					,"negations"=>implode(",", $form->getValue('dicoIdsNgt'))		
					);
				//print_r($arrDicos);		
				$moteur->arrDicos = $arrDicos;		
				$moteur->Verification($form->getValue('valeur'));
	        		        	
				$this->view->NbItem = $moteur->ordre;
				$this->view->Potentiel = $moteur->potentiel;
				$this->view->Detail = $moteur->detail;
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

