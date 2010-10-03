<?php
class Model_DbTable_Determinants extends Zend_Db_Table_Abstract
{
    protected $_name = 'gen_determinants';
    protected $_referenceMap    = array(
        'Verbe' => array(
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
    
    public function ajouterDeterminant($idDico, $num, $ordre, $lib)
    {
    	$data = array(
            'id_dico' => $idDico,
            'num' => $num,
    		'ordre' => $ordre,
            'lib' => $lib
        );
        $this->insert($data);
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
