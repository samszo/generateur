<?php
class Model_DbTable_Dicos extends Zend_Db_Table_Abstract
{
    protected $_name = 'gen_dicos';
	protected $_dependentTables = array('Model_DbTable_Verbes');

    public function getItemsDico($id)
    {
        $id = (int)$id;
		$Rowset = $this->find($id);
		$dico = $Rowset->current();
		if($dico['type']=='conjugaisons')
			$items = $dico->findDependentRowset('Model_DbTable_Verbes');
		if($dico['type']=='déterminants')
			$items = $dico->findDependentRowset('Model_DbTable_Determinants');
		if($dico['type']=='compléments')
			$items = $dico->findDependentRowset('Model_DbTable_Complements');
		if($dico['type']=='syntagmes')
			$items = $dico->findDependentRowset('Model_DbTable_Syntagmes');
		if($dico['type']=='concepts')
			$items = $dico->findDependentRowset('Model_DbTable_Concepts');
			
        return $items;
    }
	
	public function obtenirDico($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('id_dico = ' . $id);
        if (!$row) {
            throw new Exception("Count not find row $id");
        }
        return $row->toArray();
    }

    public function ajouterDico($url, $type, $urlS="", $pathS="")
    {
        //mettre utf8_decode pour php 5.3
    	$data = array(
            'url' => $url,
            'url_source' => $urlS,
            'path_source' => $pathS,
    		'type' => $type
        );
        return $this->insert($data);
    }
    
    public function modifierDico($id, $url, $type, $urlS)
    {
        $data = array(
            'url' => $url,
            'url_source' => $urlS,
        	'type' => $type,
            'maj' => new Zend_Db_Expr('CURDATE()')
        );
        $this->update($data, 'id_dico = '. (int)$id);
    }

    public function supprimerDico($id)
    {
        $this->delete('id_dico =' . (int)$id);
    }
    
}