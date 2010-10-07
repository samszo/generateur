<?php
class Model_DbTable_Concepts extends Zend_Db_Table_Abstract
{
    protected $_name = 'gen_concepts';
	protected $_dependentTables = array(
		'Model_DbTable_ConceptsVerbes'
		,'Model_DbTable_ConceptsAdjectifs'
		,'Model_DbTable_ConceptsSubstantifs'
		,'Model_DbTable_ConceptsSyntagmes'
		,'Model_DbTable_ConceptsGens'
		);
	
    protected $_referenceMap    = array(
        'Dico' => array(
            'columns'           => 'id_dico',
            'refTableClass'     => 'Model_DbTable_Dicos',
            'refColumns'        => 'id_dico'
        )
    );	
	
    public function obtenirConcept($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('id_concept = ' . $id);
        if (!$row) {
            throw new Exception("Count not find row $id");
        }
        return $row->toArray();
    }

	public function existeConcept($idDico, $lib, $type)
    {
		$select = $this->select();
		$select->from($this, array('id_concept'))
			->where('id_dico = ?', $idDico)
			->where('lib = ?', $lib)
			->where('type = ?', $type);
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_concept; else $id=false;
        return $id;
    }    
    
    public function ajouterConcept($idDico, $lib, $type)
    {
    	$id = $this->existeConcept($idDico, $lib, $type);
    	if(!$id){
    		$data = array(
            'id_dico' => $idDico,
            'lib' => $lib,
            'type' => $type
	        );
    	 	$id = $this->insert($data);
    	}
    	return $id;
    }
    
    public function modifierConcept($id, $lib, $type)
    {
        $data = array(
            'lib' => $lib,
            'type' => $type
        );
        $this->update($data, 'id_concept = '. (int)$id);
    }

    public function supprimerConcept($id)
    {
		$Rowset = $this->find($id);
		$parent = $Rowset->current();
		//a faire en bouclant sur les $_dependentTables
		/*
		$enfants = $parent->findDependentRowset('Model_DbTable_Terminaisons');
    	$tEnfs = new Model_DbTable_Terminaisons;
		foreach($enfants as $enf){
    		$tEnfs->supprimerTerminaison($enf["id_trm"]);	
    	}
		*/
    	$this->delete('id_concept =' . (int)$id);
    }
}
