<?php
class Model_DbTable_Verbes extends Zend_Db_Table_Abstract
{
    protected $_name = 'gen_verbes';
	protected $_dependentTables = array('Model_DbTable_Terminaisons');

    protected $_referenceMap    = array(
        'Dico' => array(
            'columns'           => 'id_dico',
            'refTableClass'     => 'Model_DbTable_Dicos',
            'refColumns'        => 'id_dico'
        )
    );	
	
    public function obtenirVerbe($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('id_verbe = ' . $id);
        if (!$row) {
            throw new Exception("Count not find row $id");
        }
        return $row->toArray();
    }

    public function obtenirDicoVerbes($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('id_dico = ' . $id);
        if (!$row) {
            throw new Exception("Count not find row $id");
        }
        return $row->toArray();
    }
    
    public function ajouterVerbe($idDico, $num, $modele)
    {
    	$data = array(
            'id_dico' => $idDico,
            'num' => $num,
            'modele' => $modele
        );
        return $this->insert($data);
    }
    
    public function modifierVerbe($id, $num, $modele)
    {
        $data = array(
            'num' => $num,
            'modele' => $modele
        );
        $this->update($data, 'id_verbe = '. (int)$id);
    }

    public function supprimerVerbe($id)
    {
		$Rowset = $this->find($id);
		$parent = $Rowset->current();
		$enfants = $parent->findDependentRowset('Model_DbTable_Terminaisons');
    	$tEnfs = new Model_DbTable_Terminaisons;
		foreach($enfants as $enf){
    		$tEnfs->supprimerTerminaison($enf["id_trm"]);	
    	}
    	$this->delete('id_verbe =' . (int)$id);
    }
}
