<?php
class Model_DbTable_Dicos extends Zend_Db_Table_Abstract
{
    protected $_name = 'gen_dicos';
	protected $_dependentTables = array('Model_DbTable_Verbes');

    public function obtenirDicos($id)
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
class Model_DbTable_Verbes extends Zend_Db_Table_Abstract
{
    protected $_name = 'gen_verbes';
	protected $_dependentTables = array('gen_terminaisons');

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
        $this->delete('id_verbe =' . (int)$id);
    }
}
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
