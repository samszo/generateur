<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
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
	    
	    //$this->modifierAction();
	    
	    //$this->supprimerAction();
	    
    }

    public function modifierAction()
    {

        $id = $this->_getParam('id', 0);
        $type = $this->_getParam('type', 0);
    	/*
        $echo =false;
        Zend_Debug::dump($id, $echo, $echo);
        Zend_Debug::dump($type, $label = null, $echo = true);
		*/
        if($type=='dico'){
	        $table = new Model_DbTable_Dicos();
			$Rowset = $table->find($id);
			$parent = $Rowset->current();
			$enfants = $parent->findDependentRowset('Model_DbTable_Verbes');
			$types = array("parent"=>"dico","enfant"=>"verbe");	            	
			$this->view->title = "Modification du ".$type." (".$id.")";
			$this->view->libAjout = "Ajouter un nouveau verbe";
        }
        if($type=='verbe'){
	        $table = new Model_DbTable_Verbes();
			$Rowset = $table->find($id);
			$parent = $Rowset->current();
			$enfants = $parent->findDependentRowset('Model_DbTable_Terminaisons');
			$types = array("parent"=>"verbe","enfant"=>"terminaison");
		    //ajout du formulaire pour modifier l'élément parent
			$form = new Form_Verbe(array("id"=>$id));
		    $form->envoyer->setLabel('Modifier');
	        $form->populate($parent->toArray());
		    $this->view->form = $form;		    
			$this->view->title = "Modification du ".$type." (".$id.")";
			$this->view->libAjout = "Ajouter une nouvelle terminaison";
        }
        if($type=='terminaison'){
	        $table = new Model_DbTable_Terminaisons();
			$Rowset = $table->find($id);
			$parent = $Rowset->current();
			$enfants = $Rowset->current();
			$types = array("parent"=>"terminaison","enfant"=>"terminaison");	            	
			$this->view->title = "Modification de la ".$type." (".$id.")";
			$this->view->libAjout = "";
		    //ajout du formulaire pour modifier l'élément parent
			$form = new Form_Terminaison(array("id"=>$id));
		    $form->envoyer->setLabel('Modifier');
	        $form->populate($parent->toArray());
		    $this->view->form = $form;		    
        }
        
		$this->view->cols = $enfants->getTable()->info('cols');
        $this->view->key = $enfants->getTable()->info('primary');
	    $this->view->headTitle($this->view->title, 'PREPEND');
		$this->view->parent = $parent;
		$this->view->enfants = $enfants;
		$this->view->types = $types;
		
        if($type!='dico'){
			if ($this->getRequest()->isPost()) {
		        $formData = $this->getRequest()->getPost();
		        if ($form->isValid($formData)) {
		        	if($type=="verbe"){
						$dbV = new Model_DbTable_Verbes();
						$dbV->modifierVerbe($form->getValue('id'),$form->getValue('num'),$form->getValue('modele'));
						$this->_redirect('/index/modifier/type/verbe/id/'.$id);
		        	}
		        	if($type=="terminaison"){
						$dbT = new Model_DbTable_Terminaisons();
						$dbT->modifierTerminaison($form->getValue('id'),$form->getValue('num'),$form->getValue('lib'));
						$this->_redirect('/index/modifier/type/terminaison/id/'.$id);
		        	}
		        }else{
		            $form->populate($formData);
		        }
		    }
        }		
		
		
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
	        	$this->_redirect('/index/modifier/id/'.$id.'/type/dico');
	        }else{
	        	$this->_redirect('/');
	        }
	    } else {
	        $id = $this->_getParam('id', 0);
	        $dicos = new Model_DbTable_Dicos();
	        $this->view->dico = $dicos->obtenirDico($id);
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
	        $this->view->dico = $dicos->obtenirDico($id);
	    }
    }
    
    public function ajouterAction()
    {
        $type = $this->_getParam('type', 0);
        $id = $this->_getParam('id', 0);
        
	    $this->view->headTitle($this->view->title, 'PREPEND');
	
	    if($type=="dico")
		    $form = new Form_Dico();
			$this->view->types = array("parent"=>"dico");	            	
        	$this->view->title = "Ajouter un nouveau ".$type;
		if($type=="verbe"){
		    $form = new Form_Verbe(array("id"=>$id));
			$dicos = new Model_DbTable_Dicos();
	        $this->view->parent = $dicos->obtenirDico($id);
			$this->view->types = array("parent"=>"dico","enfant"=>"verbe");	            	
        	$this->view->title = "Ajouter un nouveau ".$type;
		}
		if($type=="terminaison"){
		    $form = new Form_Terminaison(array("id"=>$id));
			$verbes = new Model_DbTable_Verbes();
	        $this->view->parent = $verbes->obtenirVerbe($id);
			$this->view->types = array("parent"=>"verbe","enfant"=>"terminaison");	            	
        	$this->view->title = "Ajouter une nouvelle ".$type;
		}
	    
	    $form->envoyer->setLabel('Ajouter');
	    $this->view->form = $form;
	
	    if ($this->getRequest()->isPost()) {
	        $formData = $this->getRequest()->getPost();
	        if ($form->isValid($formData)) {

	        	if($type=="dico"){
	        		$this->ajouterDico($form);
	        	}
	        	if($type=="verbe"){
					$db = new Model_DbTable_Verbes();
					$db->ajouterVerbe($form->getValue('id'),$form->getValue('num'),$form->getValue('model'));
					$this->_redirect('/index/modifier/type/dico/id/'.$id);
	        	}
	        	if($type=="terminaison"){
					$db = new Model_DbTable_Terminaisons();
					$db->ajouterTerminaison($form->getValue('id'),$form->getValue('num'),$form->getValue('lib'));
					$this->_redirect('/index/modifier/type/verbe/id/'.$id);
	        	}
	        }else{
	            $form->populate($formData);
	        }
	    }
    }

    
    private function ajouterDico($form){

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
    	
    }
    
    
    public function supprimerAction()
	{
        $type = $this->_getParam('type', 0);
        $id = $this->_getParam('id', 0);
        
        if($type=="terminaison")
			$this->view->title = "Supprimer la ".$type;
		else
			$this->view->title = "Supprimer le ".$type;
		$this->view->headTitle($this->view->title, 'PREPEND');
	
	    if ($this->getRequest()->isPost()) {
	        $supprimer = $this->getRequest()->getPost('supprimer');
	        if ($supprimer == 'Oui') {
	            $id = $this->getRequest()->getPost('id');
	            if($type=="dico"){
		            $dbD = new Model_DbTable_Dicos();
		            $dbD->supprimerDico($id);	            	
	            }
	            if($type=="verbe"){
		            $dbV = new Model_DbTable_Verbes();
		            $dbV->supprimerVerbe($id);	            	
	            }
	            if($type=="terminaison"){
		            $dbT = new Model_DbTable_Terminaisons();
		            $dbT->supprimerTerminaison($id);	            	
	            }
	        }
	        if($type=="dico") $this->_redirect('/');
	        if($type=="verbe") $this->_redirect('/index/modifier/type/dico/id/'.$this->_getParam('idParent', 0));
	        if($type=="terminaison") $this->_redirect('/index/modifier/type/verbe/id/'.$this->_getParam('idParent', 0));
	    } else {
            if($type=="dico"){
	            $dicos = new Model_DbTable_Dicos();
		        $this->view->parent = $dicos->obtenirDico($id);
				$this->view->types = array("parent"=>"dico");	            	
		        $this->view->id = $id;
            }
            if($type=="verbe"){
	            $verbes = new Model_DbTable_Verbes();
		        $this->view->parent = $verbes->obtenirVerbe($id);
				$this->view->types = array("parent"=>"verbe");	            	
		        $this->view->id = $id;
		        $this->view->idParent = $this->view->parent["id_dico"];
            }	        
            if($type=="terminaison"){
	            $terms = new Model_DbTable_Terminaisons();
		        $this->view->parent = $terms->obtenirTerminaison($id);
				$this->view->types = array("parent"=>"terminaison");	            	
		        $this->view->id = $id;
		        $this->view->idParent = $this->view->parent["id_verbe"];
            }	        
	    }
	}    
}



