<?php
class Model_DbTable_Complements extends Zend_Db_Table_Abstract
{
    protected $_name = 'gen_complements';
    protected $_referenceMap    = array(
        'Verbe' => array(
            'columns'           => 'id_dico',
            'refTableClass'     => 'Model_DbTable_Dicos',
            'refColumns'        => 'id_dico'
        )
    );	
    
    public function obtenirComplement($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('id_cpm = ' . $id);
        if (!$row) {
            throw new Exception("Count not find row $id");
        }
        return $row->toArray();
    }
    
    public function ajouterComplement($idDico, $num, $ordre, $lib)
    {
    	$data = array(
            'id_dico' => $idDico,
            'num' => $num,
    		'ordre' => $ordre,
            'lib' => $lib
        );
        $this->insert($data);
    }
    
    public function modifierComplement($id, $num, $ordre, $lib)
    {
        $data = array(
            'num' => $num,
    		'ordre' => $ordre,
        	'lib' => $lib
        );
        $this->update($data, 'id_cpm = '. (int)$id);
    }

    public function supprimerComplement($id)
    {
        $this->delete('id_cpm =' . (int)$id);
    }
}
