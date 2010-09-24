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
	    $this->view->title = "Mes Dicos";
	    $this->view->headTitle($this->view->title, 'PREPEND');
	    $dicos = new Model_DbTable_Dicos();
	    $this->view->dicos = $dicos->fetchAll();
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
	        	$adapter->setDestination(implode(PATH_SEPARATOR.APPLICATION_PATH . '/../data/upload'));
	        	       			
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
						echo "info".print_r($info);
					}
	      		}
	        	$type = $form->getValue('type');
	        	$url = $form->url->getFileName();
	        	
	            $dicos = new Model_DbTable_Dicos();
	           	$dicos->ajouterDico($url, $type);

	            $this->_redirect('/');
	        } else {
	            $form->populate($formData);
	        }
	    }
    }
    
}



