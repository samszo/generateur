<?php
class Model_DbTable_Terminaisons extends Zend_Db_Table_Abstract
{
    protected $_name = 'gen_terminaisons';
    protected $_referenceMap    = array(
        'Verbe' => array(
            'columns'           => 'id_verbe',
            'refTableClass'     => 'Model_DbTable_Verbes',
            'refColumns'        => 'id_verbe'
        )
    );	
    
    public function obtenirTerminaison($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('id_trm = ' . $id);
        if (!$row) {
            throw new Exception("Count not find row $id");
        }
        return $row->toArray();
    }

    public function obtenirVerbeTerminaisons($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('id_verbe = ' . $id);
        if (!$row) {
            throw new Exception("Count not find row $id");
        }
        return $row->toArray();
    }
    
    public function ajouterTerminaison($idVerbe, $num, $lib)
    {
    	$data = array(
            'id_verbe' => $idVerbe,
            'num' => $num,
            'lib' => $lib
        );
        $this->insert($data);
    }
    
    public function modifierTerminaison($id, $num, $lib)
    {
        $data = array(
            'num' => $num,
            'lib' => $lib
        );
        $this->update($data, 'id_trm = '. (int)$id);
    }

    public function supprimerVerbe($id)
    {
        $this->delete('id_trm =' . (int)$id);
    }
}
