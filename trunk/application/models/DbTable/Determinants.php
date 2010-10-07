<?php
class Model_DbTable_Determinants extends Zend_Db_Table_Abstract
{
    protected $_name = 'gen_determinants';
    protected $_referenceMap    = array(
        'Conjugaison' => array(
            'columns'           => 'id_dico',
            'refTableClass'     => 'Model_DbTable_Dicos',
            'refColumns'        => 'id_dico'
        )
    );	
    
    public function obtenirDeterminant($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('id_dtm = ' . $id);
        if (!$row) {
            throw new Exception("Count not find row $id");
        }
        return $row->toArray();
    }
    
	public function existeDeterminant($idDico, $num, $ordre, $lib)
    {
		$select = $this->select();
		$select->from($this, array('id_dtm'))
			->where('id_dico = ?', $idDico)
			->where('lib = ?', $lib)
			->where('ordre = ?', $ordre)
			->where('num = ?', $num);
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_dtm; else $id=-1;
        return $id;
    }    
    
    public function ajouterDeterminant($idDico, $num, $ordre, $lib)
    {
    	$id = $this->existeDeterminant($idDico, $num, $ordre, $lib);
    	if(!$id){
    		$data = array(
            'id_dico' => $idDico,
            'num' => $num,
    		'ordre' => $ordre,
            'lib' => $lib
	        );
    	 	$id = $this->insert($data);
    	}
    	return $id;
    }
    
    public function modifierDeterminant($id, $num, $ordre, $lib)
    {
        $data = array(
            'num' => $num,
    		'ordre' => $ordre,
        	'lib' => $lib
        );
        $this->update($data, 'id_dtm = '. (int)$id);
    }

    public function supprimerDeterminant($id)
    {
        $this->delete('id_dtm =' . (int)$id);
    }
}
