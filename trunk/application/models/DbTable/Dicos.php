<?php
class Model_DbTable_Dicos extends Zend_Db_Table_Abstract
{
    protected $_name = 'gen_dicos';

    public function obtenirDicos($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('id_dico = ' . $id);
        if (!$row) {
            throw new Exception("Count not find row $id");
        }
        return $row->toArray();
    }

    public function ajouterDico($url, $type)
    {
        $data = array(
            'url' => $url,
            'type' => $type
        );
        $this->insert($data);
    }

    public function modifierDico($id, $artiste, $titre)
    {
		$date = new Zend_Date($unixtimestamp, Zend_Date::TIMESTAMP);
        $data = array(
            'url' => $url,
            'type' => $type,
            'maj' => $date
        );
        $this->update($data, 'id_dico = '. (int)$id);
    }

    public function supprimerDico($id)
    {
        $this->delete('id_dico =' . (int)$id);
    }
}
