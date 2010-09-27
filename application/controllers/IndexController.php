<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
	    $this->view->title = "Mes Dictionnaires";
	    $this->view->headTitle($this->view->title, 'PREPEND');
	    $dicos = new Model_DbTable_Dicos();
	    $this->view->dicos = $dicos->fetchAll();		 
		
		//pour le débuggage
	    //$form = new Form_DicoAjout();
	    //$form->envoyer->setLabel('Ajouter');
	    //$this->view->form = $form;
	    
	    //$dico = new Gen_Dico();
	    //$dico->Traite("C:\wamp\www\generateur\dico\DS_conjugaisonsTot", "conjugaisons");
		//$dico->GetMacToXml(3);
		//$dico->SaveBdd(1);
	    
	    
    }

    public function modifierAction()
    {
		$this->view->title = "Modification du dictionnaire";
	    $this->view->headTitle($this->view->title, 'PREPEND');

        $id = $this->_getParam('id', 0);
        $dicos = new Model_DbTable_Dicos();
		$dicoRowset = $dicos->find($id);
		$dico = $dicoRowset->current();
		$verbesByDico = $dico->findDependentRowset('Model_DbTable_Verbes');	    
        
        $this->view->verbes = $verbesByDico;
        $this->view->dico = $dico;
        
    }
    
    public function sauvegarderAction()
    {
		$this->view->title = "Sauvegarde du dictionaire XML";
	    $this->view->headTitle($this->view->title, 'PREPEND');

	    if ($this->getRequest()->isPost()) {
	        $calculer = $this->getRequest()->getPost('sauvegarder');
	        if ($calculer == 'Oui') {
	            $id = $this->getRequest()->getPost('id');
	            //echo "idDico = ".$id."<br/>";
			    $dico = new Gen_Dico();
				$dico->SaveBdd($id);
	        }else{
	        	$this->_redirect('/');
	        }
	    } else {
	        $id = $this->_getParam('id', 0);
	        $dicos = new Model_DbTable_Dicos();
	        $this->view->dico = $dicos->obtenirDicos($id);
	    }
    }
    
    public function creerxmlAction()
    {
		$this->view->title = "Création du dictionnaire au format XML";
	    $this->view->headTitle($this->view->title, 'PREPEND');

	    if ($this->getRequest()->isPost()) {
	        $calculer = $this->getRequest()->getPost('calculer');
	        if ($calculer == 'Oui') {
	            $id = $this->getRequest()->getPost('id');
	            //echo "idDico = ".$id."<br/>";
			    $dico = new Gen_Dico();
				$dico->GetMacToXml($id);
				$this->view->xml = $dico->xml->saveHTML();
	        	$this->_redirect('index/sauvegarder/id/'.$id);
	        }else{
	        	$this->_redirect('/');
	        }
	    } else {
	        $id = $this->_getParam('id', 0);
	        $dicos = new Model_DbTable_Dicos();
	        $this->view->dico = $dicos->obtenirDicos($id);
	    }
    }
    
    public function ajouterAction()
    {
        $this->view->title = "Ajouter un nouveau dictionnaire";
	    $this->view->headTitle($this->view->title, 'PREPEND');
	
	    $form = new Form_Dico();
	    $form->envoyer->setLabel('Ajouter');
	    $this->view->form = $form;
	
	    if ($this->getRequest()->isPost()) {
	        $formData = $this->getRequest()->getPost();
	        if ($form->isValid($formData)) {

	        	$adapter = new Zend_File_Transfer_Adapter_Http();
	        	$adapter->setDestination(ROOT_PATH.'\data\upload');
	        	       			
				if (!$adapter->receive()) {
					$messages = $adapter->getMessages();
					echo implode("\n", $messages);
	      		}else{
					// Retourne toutes les informations connues sur le fichier
					$files = $adapter->getFileInfo();
					foreach ($files as $file => $info) {
						// Les validateurs sont-ils OK ?
						if (!$adapter->isValid($file)) {
							print "Désolé mais $file ne correspond à ce que nous attendons";
							continue;
						}
						//echo "infos:".print_r($info);
					}
	      		}
	        	$type = $form->getValue('type');
	        	$url = str_replace(ROOT_PATH,WEB_ROOT,$adapter->getFileName());
	        	$url = str_replace("\\","/",$url);
	           	$dico = new Gen_Dico();
	           	$dico->urlS = $url;
	           	$dico->pathS = $adapter->getFileName();
	           	$dico->type = $type;
	           	$dico->Save();
	           	
	            $this->_redirect('/');
	            //$this->_redirect($this->url);
	            
	        } else {
	            $form->populate($formData);
	        }
	    }
    }

    public function supprimerAction()
	{
	    $this->view->title = "Supprimer le dictionnaire";
	    $this->view->headTitle($this->view->title, 'PREPEND');
	
	    if ($this->getRequest()->isPost()) {
	        $supprimer = $this->getRequest()->getPost('supprimer');
	        if ($supprimer == 'Oui') {
	            $id = $this->getRequest()->getPost('id');
	            $dicos = new Model_DbTable_Dicos();
	            $dicos->supprimerDico($id);
	        }
	        $this->_redirect('/');
	    } else {
	        $id = $this->_getParam('id', 0);
	        $dicos = new Model_DbTable_Dicos();
	        $this->view->dico = $dicos->obtenirDicos($id);
	    }
	}    
}



