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
	    
	    //
	    //$dico = new Gen_Dico();
		//$dico->GetMacToXml(4);
		//$dico->SaveBdd(1);
		//	    
	    
	    //$this->modifierAction();
	    
	    //$this->supprimerAction();
	    
	    //$this->creerxmlAction();
    }

    public function modifierAction()
    {
        $id = $this->_getParam('id', 0);
        $type = $this->_getParam('type', 0);//
    	/*
        $echo =false;
        Zend_Debug::dump($id, $echo, $echo);
        Zend_Debug::dump($type, $label = null, $echo = true);
		*/
        if($type=='dico'){
	        $table = new Model_DbTable_Dicos();
			$Rowset = $table->find($id);
			$parent = $Rowset->current();
			if($parent['type']=='conjugaisons'){
				$enfants = $parent->findDependentRowset('Model_DbTable_Conjugaisons');
				$types = array("parent"=>"dico","enfant"=>"conjugaison");	            	
				$this->view->libAjout = "Ajouter une nouvelle conjugaison";
			}
			if($parent['type']=='déterminants'){
				$enfants = $parent->findDependentRowset('Model_DbTable_Determinants');
				$types = array("parent"=>"dico","enfant"=>"determinant");	            	
				$this->view->libAjout = "Ajouter un nouveau déterminant";
			}
			if($parent['type']=='compléments'){
				$enfants = $parent->findDependentRowset('Model_DbTable_Complements');
				$types = array("parent"=>"dico","enfant"=>"complement");	            	
				$this->view->libAjout = "Ajouter un nouveau complément";
			}
			if($parent['type']=='syntagmes'){
				$enfants = $parent->findDependentRowset('Model_DbTable_Syntagmes');
				$types = array("parent"=>"dico","enfant"=>"syntagme");	            	
				$this->view->libAjout = "Ajouter un nouveau syntagme";
			}
			if($parent['type']=='concepts'){
				$enfants = $parent->findDependentRowset('Model_DbTable_Concepts');
				$types = array("parent"=>"dico","enfant"=>"concept");	            	
				$this->view->libAjout = "Ajouter un nouveau concept";
			}
			$this->view->title = "Modification du ".$type." (".$id.")";
        }
        if($type=='conjugaison'){
	        $table = new Model_DbTable_Conjugaisons();
			$Rowset = $table->find($id);
			$parent = $Rowset->current();
			$enfants = $parent->findDependentRowset('Model_DbTable_Terminaisons');
			$types = array("parent"=>"conjugaison","enfant"=>"terminaison");
		    //ajout du formulaire pour modifier l'élément parent
			$form = new Form_Conjugaison(array("id"=>$id));
		    $form->envoyer->setLabel('Modifier');
	        $form->populate($parent->toArray());
		    $this->view->form = $form;		    
			$this->view->title = "Modification de la ".$type." (".$id.")";
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
        if($type=='determinant'){
	        $table = new Model_DbTable_Determinants();
			$Rowset = $table->find($id);
			$parent = $Rowset->current();
			$enfants = $Rowset->current();
			$types = array("parent"=>"determinant","enfant"=>"determinant");	            	
			$this->view->title = "Modification du déterminant (".$id.")";
			$this->view->libAjout = "";
		    //ajout du formulaire pour modifier l'élément parent
			$form = new Form_Determinant(array("id"=>$id));
		    $form->envoyer->setLabel('Modifier');
	        $form->populate($parent->toArray());
		    $this->view->form = $form;		    
        }
        if($type=='complement'){
	        $table = new Model_DbTable_Complements();
			$Rowset = $table->find($id);
			$parent = $Rowset->current();
			$enfants = $Rowset->current();
			$types = array("parent"=>"complement","enfant"=>"complement");	            	
			$this->view->title = "Modification du complément (".$id.")";
			$this->view->libAjout = "";
		    //ajout du formulaire pour modifier l'élément parent
			$form = new Form_Complement(array("id"=>$id));
		    $form->envoyer->setLabel('Modifier');
	        $form->populate($parent->toArray());
		    $this->view->form = $form;		    
        }
        if($type=='syntagme'){
	        $table = new Model_DbTable_Syntagmes();
			$Rowset = $table->find($id);
			$parent = $Rowset->current();
			$enfants = $Rowset->current();
			$types = array("parent"=>"syntagme","enfant"=>"syntagme");	            	
			$this->view->title = "Modification du syntagme (".$id.")";
			$this->view->libAjout = "";
		    //ajout du formulaire pour modifier l'élément parent
			$form = new Form_Syntagme(array("id"=>$id));
		    $form->envoyer->setLabel('Modifier');
	        $form->populate($parent->toArray());
		    $this->view->form = $form;		    
        }
        if($type=='concept'){
	        $table = new Model_DbTable_Concepts();
			$Rowset = $table->find($id);
			$parent = $Rowset->current();
			//charge les enfants suivant le type de concept
			if($parent->type=="a")$tType="Adjectifs";
			if($parent->type=="v")$tType="Verbes";
			if($parent->type=="m")$tType="Substantifs";
			if($parent->type=="s")$tType="Syntagmes";
			$enfants = $parent->findManyToManyRowset('Model_DbTable_'.$tType,
                                                 'Model_DbTable_Concepts'.$tType);
			$types = array("parent"=>"concept","enfant"=>strtolower(substr($tType,0,-1)));	            	
			$this->view->title = "Modification du concept (".$id.")";
			$this->view->libAjout = "Ajouter $tType";
			//ajout du formulaire pour modifier l'élément parent
			$form = new Form_Concept(array("id"=>$id));
		    $form->envoyer->setLabel('Modifier');
	        $form->populate($parent->toArray());
		    $this->view->form = $form;		    
        }
        if($type=='adjectif'){
	        $table = new Model_DbTable_Adjectifs();
			$Rowset = $table->find($id);
			$parent = $Rowset->current();
			$pRs = $parent->findManyToManyRowset('Model_DbTable_Concepts','Model_DbTable_ConceptsAdjectifs');
			$pR = $pRs->current();
			$this->view->idParent=$pR['id_concept'];
			$enfants = $Rowset->current();
			$types = array("parent"=>$type,"enfant"=>$type);	            	
			$this->view->title = "Modification de l'adjectif (".$id.")";
			$this->view->libAjout = "";
		    //ajout du formulaire pour modifier l'élément parent
			$form = new Form_Adjectif(array("id"=>$id));
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
		        print_r($form);
		        if ($form->isValid($formData)) {
		        	if($type=="conjugaison"){
						$dbCo = new Model_DbTable_Conjugaisons();
						$dbCo->modifierConjugaison($form->getValue('id'),$form->getValue('num'),$form->getValue('modele'));
		        	}
		        	if($type=="terminaison"){
						$dbT = new Model_DbTable_Terminaisons();
						$dbT->modifierTerminaison($form->getValue('id'),$form->getValue('num'),$form->getValue('lib'));
		        	}
		        	if($type=="determinant"){
						$dbD = new Model_DbTable_Determinants();
						$dbD->modifierDeterminant($form->getValue('id'),$form->getValue('num'),$form->getValue('ordre'),$form->getValue('lib'));
		        	}
		        	if($type=="complement"){
						$dbC = new Model_DbTable_Complements();
						$dbC->modifierComplement($form->getValue('id'),$form->getValue('num'),$form->getValue('ordre'),$form->getValue('lib'));
		        	}
		        	if($type=="syntagme"){
						$dbS = new Model_DbTable_Syntagmes();
						$dbS->modifierSyntagme($form->getValue('id'),$form->getValue('num'),$form->getValue('ordre'),$form->getValue('lib'));
		        	}
		        	if($type=="concept"){
						$dbCpt = new Model_DbTable_Concepts();
						$dbCpt->modifierConcept($form->getValue('id'),$form->getValue('lib'),$form->getValue('type'));
		        	}
		        	if($type=="adjectif"){
						$dbAdj = new Model_DbTable_Adjectifs();
						$dbAdj->modifierAdjectif($form->getValue('id'),$form->getValue('elision'),$form->getValue('prefix'),$form->getValue('m_s'),$form->getValue('f_s'),$form->getValue('m_p'),$form->getValue('f_p'));
		        	}
		        	$this->_redirect('/index/modifier/type/'.$type.'/id/'.$id);
		        }else{
		            $form->populate($formData);
		        }
		    }
        }		
		
		
    }
    
    public function sauvegarderAction()
    {
		$this->view->title = "Sauvegarde du dictionaire XML dans la BDD";
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
				//$this->view->xml = $dico->xml->saveHTML();
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
		if($type=="conjugaison"){
		    $form = new Form_Verbe(array("id"=>$id));
			$dicos = new Model_DbTable_Dicos();
	        $this->view->parent = $dicos->obtenirDico($id);
			$this->view->types = array("parent"=>"dico","enfant"=>$type);	            	
        	$this->view->title = "Ajouter une nouvelle ".$type;
		}
		if($type=="terminaison"){
		    $form = new Form_Terminaison(array("id"=>$id));
			$conjs = new Model_DbTable_Conjugaisons();
	        $this->view->parent = $conjs->obtenirConjugaison($id);
			$this->view->types = array("parent"=>"conjugaison","enfant"=>$type);	            	
        	$this->view->title = "Ajouter une nouvelle ".$type;
		}
		if($type=="determinant"){
		    $form = new Form_Determinant(array("id"=>$id));
			$dicos = new Model_DbTable_Dicos();
	        $this->view->parent = $dicos->obtenirDico($id);
		    $this->view->types = array("parent"=>"dico","enfant"=>$type);	            	
        	$this->view->title = "Ajouter un nouveau ".$type;
		}
		if($type=="complement"){
		    $form = new Form_Complement(array("id"=>$id));
			$dicos = new Model_DbTable_Dicos();
	        $this->view->parent = $dicos->obtenirDico($id);
		    $this->view->types = array("parent"=>"dico","enfant"=>$type);	            	
        	$this->view->title = "Ajouter un nouveau ".$type;
		}
		if($type=="syntagme"){
		    $form = new Form_Syntagme(array("id"=>$id));
			$dicos = new Model_DbTable_Dicos();
	        $this->view->parent = $dicos->obtenirDico($id);
		    $this->view->types = array("parent"=>"dico","enfant"=>$type);	            	
        	$this->view->title = "Ajouter un nouveau ".$type;
		}
		if($type=="concept"){
		    $form = new Form_Concept(array("id"=>$id));
			$dicos = new Model_DbTable_Dicos();
	        $this->view->parent = $dicos->obtenirDico($id);
		    $this->view->types = array("parent"=>"dico","enfant"=>$type);	            	
        	$this->view->title = "Ajouter un nouveau ".$type;
		}
		if($type=="adjectif"){
		    $form = new Form_Adjectif(array("id"=>$id));
			$dbCpt = new Model_DbTable_Concepts();
	        $this->view->parent = $dbCpt->obtenirConcept($id);
		    $this->view->types = array("parent"=>"concept","enfant"=>$type);	            	
        	$this->view->title = "Ajouter un nouvel ".$type;
		}
		
	    $form->envoyer->setLabel('Ajouter');
	    $this->view->form = $form;
	
	    if ($this->getRequest()->isPost()) {
	        $formData = $this->getRequest()->getPost();
	        if ($form->isValid($formData)) {

	        	if($type=="dico"){
	        		$this->ajouterDico($form);
	        	}
	        	if($type=="conjugaison"){
					$db = new Model_DbTable_Conjugaisons();
					$db->ajouterConjugaison($form->getValue('id'),$form->getValue('num'),$form->getValue('model'));
					$this->_redirect('/index/modifier/type/dico/id/'.$id);
	        	}
	        	if($type=="terminaison"){
					$db = new Model_DbTable_Terminaisons();
					$db->ajouterTerminaison($form->getValue('id'),$form->getValue('num'),$form->getValue('lib'));
					$this->_redirect('/index/modifier/type/conjugaison/id/'.$id);
	        	}
	        	if($type=="determinant"){
					$dbD = new Model_DbTable_Determinants();
					$dbD->ajouterDeterminant($form->getValue('id'),$form->getValue('num'),$form->getValue('ordre'),$form->getValue('lib'));
					$this->_redirect('/index/modifier/type/dico/id/'.$id);
	        	}
	        	if($type=="complement"){
					$dbC = new Model_DbTable_Complements();
					$dbC->ajouterComplement($form->getValue('id'),$form->getValue('num'),$form->getValue('ordre'),$form->getValue('lib'));
					$this->_redirect('/index/modifier/type/dico/id/'.$id);
	        	}
	        	if($type=="syntagme"){
					$dbS = new Model_DbTable_Syntagmes();
					$dbS->ajouterSyntagme($form->getValue('id'),$form->getValue('num'),$form->getValue('ordre'),$form->getValue('lib'));
					$this->_redirect('/index/modifier/type/dico/id/'.$id);
	        	}
	        	if($type=="concept"){
					$dbCpt = new Model_DbTable_Concepts();
					$dbCpt->ajouterConcept($form->getValue('id'),$form->getValue('lib'),$form->getValue('type'));
					$this->_redirect('/index/modifier/type/dico/id/'.$id);
	        	}
	        	if($type=="adjectif"){
					$dbCpt = new Model_DbTable_Concepts();
	        		$rs = $dbCpt->find($form->getValue('id'));
					$cpt = $rs->current();

					$dbAdj = new Model_DbTable_Adjectifs();
					$idAdj = $dbAdj->ajouterAdjectif($cpt['id_dico'],$form->getValue('elision'),$form->getValue('prefix'),$form->getValue('m_s'),$form->getValue('f_s'),$form->getValue('m_p'),$form->getValue('f_p'));

					$dbCptAdj = new Model_DbTable_ConceptsAdjectifs();
					$dbCptAdj->ajouterConceptAdjectif($cpt['id_concept'], $idAdj);

					$this->_redirect('/index/modifier/type/concept/id/'.$id);
	        	}
	        }else{
	            $form->populate($formData);
	        }
	    }
    }

    
    private function ajouterDico($form){

	try {
    	
    	$adapter = new Zend_File_Transfer_Adapter_Http();
        echo ROOT_PATH.'/data/upload';
    	$adapter->setDestination(ROOT_PATH.'/data/upload');
               			
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
	}catch (Zend_Exception $e) {
          // Appeler Zend_Loader::loadClass() sur une classe non-existante
          //entrainera la levée d'une exception dans Zend_Loader
          echo "Récupère exception: " . get_class($e) . "\n";
          echo "Message: " . $e->getMessage() . "\n";
          // puis tout le code nécessaire pour récupérer l'erreur
	}
        
    }
    
    
    public function supprimerAction()
	{
	try {
		
		$type = $this->_getParam('type', 0);
        $id = $this->_getParam('id', 0);
        
        if($type=="terminaison" || $type=="conjugaison")
			$this->view->title = "Supprimer la ".$type;
        elseif($type=="adjectif")
			$this->view->title = "Supprimer l'".$type;
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
	            if($type=="conjugaison"){
		            $dbConj = new Model_DbTable_Conjugaisons();
		            $dbConj->supprimerConjugaison($id);	            	
	            }
	            if($type=="terminaison"){
		            $dbT = new Model_DbTable_Terminaisons();
		            $dbT->supprimerTerminaison($id);	            	
	            }
	            if($type=="determinant"){
		            $dbDt = new Model_DbTable_Determinants();
		            $dbDt->supprimerDeterminant($id);	            	
	            }
	            if($type=="complement"){
		            $dbC = new Model_DbTable_Complements();
		            $dbC->supprimerComplement($id);	            	
	            }
	            if($type=="syntagme"){
		            $dbS = new Model_DbTable_Syntagmes();
		            $dbS->supprimerSyntagme($id);	            	
	            }
	            if($type=="concept"){
		            $dbCpt = new Model_DbTable_Concepts();
		            $dbCpt->supprimerConcept($id);	            	
	            }
	            if($type=="adjectif"){
		            $dbAdj = new Model_DbTable_Adjectifs();
		            $dbAdj->supprimerAdjectif($id);	            	
	            }
	        }
	        if($type=="dico") $this->_redirect('/');
	        if($type=="conjugaison" || $type=="determinant" || $type=="complement" || $type=="syntagme" || $type=="concept")
	        	$this->_redirect('/index/modifier/type/dico/id/'.$this->_getParam('idParent', 0));
	        if($type=="terminaison") $this->_redirect('/index/modifier/type/conjugaison/id/'.$this->_getParam('idParent', 0));
	        if($type=="adjectif") $this->_redirect('/index/modifier/type/concept/id/'.$this->_getParam('idParent', 0));
	    } else {
            if($type=="dico"){
	            $dicos = new Model_DbTable_Dicos();
		        $this->view->parent = $dicos->obtenirDico($id);
				$this->view->types = array("parent"=>$type);	            	
		        $this->view->id = $id;
            }
            if($type=="conjugaison"){
	            $conjs = new Model_DbTable_Conjugaisons();
		        $this->view->parent = $conjs->obtenirConjugaison($id);
				$this->view->types = array("parent"=>$type);	            	
		        $this->view->id = $id;
		        $this->view->idParent = $this->view->parent["id_dico"];
            }	        
            if($type=="terminaison"){
	            $terms = new Model_DbTable_Terminaisons();
		        $this->view->parent = $terms->obtenirTerminaison($id);
				$this->view->types = array("parent"=>$type);	            	
		        $this->view->id = $id;
		        $this->view->idParent = $this->view->parent["id_conj"];
            }	        
            if($type=="determinant"){
	            $deter = new Model_DbTable_Determinants();
		        $this->view->parent = $deter->obtenirDeterminant($id);
				$this->view->types = array("parent"=>$type);	            	
		        $this->view->id = $id;
		        $this->view->idParent = $this->view->parent["id_dico"];
            }	        
            if($type=="complement"){
	            $comp = new Model_DbTable_Complements();
		        $this->view->parent = $comp->obtenirComplement($id);
				$this->view->types = array("parent"=>$type);	            	
		        $this->view->id = $id;
		        $this->view->idParent = $this->view->parent["id_dico"];
            }	        
            if($type=="syntagme"){
	            $synt = new Model_DbTable_Syntagmes();
		        $this->view->parent = $synt->obtenirSyntagme($id);
				$this->view->types = array("parent"=>$type);	            	
		        $this->view->id = $id;
		        $this->view->idParent = $this->view->parent["id_dico"];
            }	        
            if($type=="concept"){
	            $cpts = new Model_DbTable_Concepts();
		        $this->view->parent = $cpts->obtenirConcept($id);
				$this->view->types = array("parent"=>$type);	            	
		        $this->view->id = $id;
		        $this->view->idParent = $this->view->parent["id_dico"];
            }	        
            if($type=="adjectif"){
	            $table = new Model_DbTable_Adjectifs();
				$Rowset = $table->find($id);
				$parent = $Rowset->current();            
		        $this->view->parent = $parent;
				$this->view->types = array("parent"=>$type);	            	
		        $this->view->id = $id;
				$pRs = $parent->findManyToManyRowset('Model_DbTable_Concepts','Model_DbTable_ConceptsAdjectifs');
				$pR = $pRs->current();
				$this->view->idParent=$pR['id_concept'];
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



