<?php
class Model_DbTable_Syntagmes extends Zend_Db_Table_Abstract
{
    protected $_name = 'gen_syntagmes';
    protected $_referenceMap    = array(
        'Verbe' => array(
            'columns'           => 'id_dico',
            'refTableClass'     => 'Model_DbTable_Dicos',
            'refColumns'        => 'id_dico'
        )
    );	
    
    public function obtenirSyntagme($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('id_syn = ' . $id);
        if (!$row) {
            throw new Exception("Count not find row $id");
        }
        return $row->toArray();
    }
    
    public function ajouterSyntagme($idDico, $num, $ordre, $lib="")
    {
    	$data = array(
            'id_dico' => $idDico,
            'num' => $num,
    		'ordre' => $ordre,
            'lib' => $lib
        );
        $this->insert($data);
    }
    
    public function modifierSyntagme($id, $num, $ordre, $lib)
    {
        $data = array(
            'num' => $num,
    		'ordre' => $ordre,
        	'lib' => $lib
        );
        $this->update($data, 'id_syn = '. (int)$id);
    }

    public function supprimerSyntagme($id)
    {
        $this->delete('id_syn =' . (int)$id);
    }
}
