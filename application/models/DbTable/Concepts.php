<?php
class Model_DbTable_Concepts extends Zend_Db_Table_Abstract
{
    protected $_name = 'gen_concepts';
	protected $_dependentTables = array(
		'Model_DbTable_ConceptsVerbes'
		,'Model_DbTable_ConceptsAdjectifs'
		,'Model_DbTable_ConceptsSubstantifs'
		,'Model_DbTable_ConceptsSyntagmes'
		,'Model_DbTable_ConceptsGenerateurs'
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

    public function obtenirConceptByDico($id)
    {
        $id = (int)$id;
        $query = $this->select()
            ->where( "id_dico = " . $id);
		$rs = $this->fetchAll($query);        
    	if (!$rs) {
            throw new Exception("Count not find rs $id");
        }
        return $rs->toArray();
    }

    public function obtenirConceptByDicoLibType($idDico,$lib,$type)
    {
        $query = $this->select()
            ->where( "id_dico IN (?)",$idDico)
        	->where( "lib = ?",$lib)
            ->where( "type = ?",$type)
            ;
		$r = $this->fetchRow($query);        
    	if (!$r) {
            throw new Exception("Count not find rs $id");
        }
        return $r;
    }
    
    public function obtenirConceptDescription($idDico, $arrClass){

			$cpt = $this->obtenirConceptByDicoLibType($idDico,$arrClass[1],$arrClass[0]);
			//cherche les enfants suivant le type de concept
			if($arrClass[0]=="a")$tType="Adjectifs";
			if($arrClass[0]=="v")$tType="Verbes";
			if($arrClass[0]=="m")$tType="Substantifs";
			if($arrClass[0]=="s")$tType="Syntagmes";
			$enfants = $cpt->findManyToManyRowset('Model_DbTable_'.$tType,
                                                 'Model_DbTable_Concepts'.$tType);
			//cherche les généreteurs
			$gens = $cpt->findManyToManyRowset('Model_DbTable_Generateurs',
	                                                 'Model_DbTable_ConceptsGenerateurs');
    	
    		return array("src"=>$cpt->toArray(),"dst"=>array_merge($enfants->toArray(),$gens->toArray()));
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
    	$id = false;//$this->existeConcept($idDico, $lib, $type);
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
    	foreach($this->_dependentTables as $t){
			$tEnfs = new $t();
			$tEnfs->supprimerConcept($id);
		}
    	$this->delete('id_concept =' . (int)$id);
    }

    public function supprimerDico($id)
    {
    	$arr = $this->obtenirConceptByDico($id);
		foreach($arr as $enf){
    		$this->supprimerConcept($enf["id_concept"]);	
    	}    	
    	$this->delete('id_dico =' . (int)$id);
    }
    
}
