<?php
/**
 * Ce fichier contient la classe Gen_oeuvres_utis.
 *
 * @copyright  2013 Samuel Szoniecky
 * @license    "New" BSD License
*/
class Model_DbTable_Gen_oeuvres_utis extends Zend_Db_Table_Abstract
{
    
    /**
     * Nom de la table.
     */
    protected $_name = 'gen_oeuvres_utis';
    
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
     * Vérifie si une entrée Gen_oeuvres_utis existe.
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
     * Ajoute une entrée Gen_oeuvres_utis.
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
     * Recherche une entrée Gen_oeuvres_utis avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
   	
    	$this->update($data, 'gen_oeuvres_utis.id_oeu = ' . $id);
    }
    
    /**
     * Recherche une entrée Gen_oeuvres_utis avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
    	$this->delete('gen_oeuvres_utis.id_oeu = ' . $id);
    }

    /**
     * Recherche les entrées de Gen_oeuvres_utis avec la clef de lieu
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
     * Récupère toutes les entrées Gen_oeuvres_utis avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
   	
    	$query = $this->select()
                    ->from( array("gen_oeuvres_utis" => "gen_oeuvres_utis") );
                    
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
     * Recherche une entrée Gen_oeuvres_utis avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_oeu
     *
     * @return array
     */
    public function findById_oeu($id_oeu)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_oeuvres_utis") )                           
                    ->where( "g.id_oeu = ?", $id_oeu );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_oeuvres_utis avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $uti_id
     *
     * @return array
     */
    public function findByUti_id($uti_id)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_oeuvres_utis") )                           
                    ->where( "g.uti_id = ?", $uti_id );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
