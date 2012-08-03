<?php
class Model_DbTable_Adjectifs extends Zend_Db_Table_Abstract
{
    protected $_name = 'gen_adjectifs';
	
    protected $_dependentTables = array('Model_DbTable_ConceptsAdjectifs');
    
    protected $_referenceMap    = array(
        'Dico' => array(
            'columns'           => 'id_dico',
            'refTableClass'     => 'Model_DbTable_Dicos',
            'refColumns'        => 'id_dico'
        )
        );	
	
    public function obtenirAdjectif($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('id_adj = ' . $id);
        if (!$row) {
            throw new Exception("Count not find row $id");
        }
        return $row->toArray();
    }

    public function obtenirAdjectifByIdConcept($id)
    {
        $query = $this->select()
			->from( array("a" => "gen_adjectifs") )                           
			->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->joinInner(array('ca' => 'gen_concepts_adjectifs'),
                'ca.id_adj = a.id_adj AND ca.id_concept = '.$id)
            
            ;

        return $this->fetchAll($query)->toArray(); 
    }
    
	public function existeAdjectif($idDico, $eli, $prefix, $ms, $fs, $mp, $fp)
    {
		$select = $this->select();
		$select->from($this, array('id_adj'))
			->where('id_dico = ?', $idDico)
			->where('elision = ?', $eli)
			->where('prefix = ?', $prefix)
			->where('m_s = ?', $ms)
			->where('f_s = ?', $fs)
			->where('m_p = ?', $mp)
			->where('f_p = ?', $fp);
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_adj; else $id=false;
        return $id;
    }    
    
    public function ajouterAdjectif($idDico, $eli, $prefix, $ms, $fs, $mp, $fp)
    {
    	$id = $this->existeAdjectif($idDico, $eli, $prefix, $ms, $fs, $mp, $fp);
    	if(!$id){
    		$data = array(
	            'id_dico' => $idDico,
	    		'elision' => $eli,
	    		'prefix' => $prefix,
	            'm_s' => $ms,
	            'f_s' => $fs,
	            'm_p' => $mp,
	            'f_p' => $fp
	    	);
    	 	$id = $this->insert($data);
    	}
    	return $id;
    }

    /**
     * Recherche une entrée avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return integer
     */
    public function edit($id, $data)
    {        
        return $this->update($data, 'id_adj = ' . $id);
    }
    
    public function modifierAdjectif($id,  $eli, $prefix, $ms, $fs, $mp, $fp)
    {
        $data = array(
    		'elision' => $eli,
    		'prefix' => $prefix,
            'm_s' => $ms,
            'f_s' => $fs,
            'm_p' => $mp,
            'f_p' => $fp
        );
        $this->update($data, 'id_adj = '. (int)$id);
    }

    public function supprimerAdjectif($id)
    {
    	$this->delete('id_adj =' . (int)$id);
    }
    
    public function supprimerDico($id)
    {
    	$this->delete('id_dico =' . (int)$id);
    }
    
    
}
