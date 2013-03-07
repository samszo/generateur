<?php
/**
 * Ce fichier contient la classe Gen_oeuvres_dicos.
 *
 * @copyright  2013 Samuel Szoniecky
 * @license    "New" BSD License
 */

class Model_DbTable_Gen_oeuvresxdicos extends Zend_Db_Table_Abstract
{
    
    /**
     * Nom de la table.
     */
    protected $_name = 'gen_oeuvres_dicos';
    
    /**
     * Clef primaire de la table.
     */
    protected $_primary = 'id_oeu';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gen_oeuvres_dicos existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_oeu'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_oeu; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gen_oeuvres_dicos.
     *
     * @param array $data
     * @param boolean $existe
     *  
     * @return integer
     */
    public function ajouter($data, $existe=true)
    {
    	
    	$id=false;
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 
           
    /**
     * Recherche une entrée Gen_oeuvres_dicos avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
   	
    	$this->update($data, 'gen_oeuvres_dicos.id_oeu = ' . $id);
    }
    
    /**
     * Recherche une entrée Gen_oeuvres_dicos avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
    	$this->delete('gen_oeuvres_dicos.id_oeu = ' . $id);
    }

    /**
     * Recherche les entrées de Gen_oeuvres_dicos avec la clef de lieu
     * et supprime ces entrées.
     *
     * @param integer $idLieu
     *
     * @return void
     */
    public function removeLieu($idLieu)
    {
		$this->delete('id_lieu = ' . $idLieu);
    }
    
    /**
     * Récupère toutes les entrées Gen_oeuvres_dicos avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
   	
    	$query = $this->select()
                    ->from( array("gen_oeuvres_dicos" => "gen_oeuvres_dicos") );
                    
        if($order != null)
        {
            $query->order($order);
        }

        if($limit != 0)
        {
            $query->limit($limit, $from);
        }

        return $this->fetchAll($query)->toArray();
    }

    
    	/**
     * Recherche une entrée Gen_oeuvres_dicos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_oeu
     *
     * @return array
     */
    public function findById_oeu($id_oeu)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_oeuvres_dicos") )                           
                    ->where( "g.id_oeu = ?", $id_oeu );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_oeuvres_dicos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_dico
     *
     * @return array
     */
    public function findById_dico($id_dico)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_oeuvres_dicos") )                           
                    ->where( "g.id_dico = ?", $id_dico );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
