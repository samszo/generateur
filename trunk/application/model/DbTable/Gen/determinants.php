<?php
/**
 * Ce fichier contient la classe Gen_determinants.
 *
 * @copyright  2013 Samuel Szoniecky
 * @license    "New" BSD License
 */

class Model_DbTable_Gen_determinants extends Zend_Db_Table_Abstract
{
    
    /**
     * Nom de la table.
     */
    protected $_name = 'gen_determinants';
    
    /**
     * Clef primaire de la table.
     */
    protected $_primary = 'id_dtm';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gen_determinants existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_dtm'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_dtm; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gen_determinants.
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
     * Recherche une entrée Gen_determinants avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param array $data
     *
     * @return void
     */
    public function editMulti($data)
    {
   		foreach ($data as $c) {
   			$this->edit($c["id_dtm"], array("lib"=>$c["lib"]));
   		}
    }
    
    
    /**
     * Recherche une entrée Gen_determinants avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
   	
    	$this->update($data, 'gen_determinants.id_dtm = ' . $id);
    }
    
    /**
     * Recherche une entrée Gen_determinants avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
    	$this->delete('gen_determinants.id_dtm = ' . $id);
    }

    /**
     * Recherche les entrées de Gen_determinants avec la clef de lieu
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
     * Récupère toutes les entrées Gen_determinants avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
   	
    	$query = $this->select()
                    ->from( array("gen_determinants" => "gen_determinants") );
                    
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
     * Recherche une entrée Gen_determinants avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_dtm
     *
     * @return array
     */
    public function findById_dtm($id_dtm)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_determinants") )                           
                    ->where( "g.id_dtm = ?", $id_dtm );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_determinants avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_dico
     *
     * @return array
     */
    public function findByIdDico($id_dico)
    {
    	$sql = "SELECT
    		d0.num  
			,d0.id_dtm id0, d0.lib ms 
			,d1.id_dtm id1, d1.lib fs
			,d2.id_dtm id2, d2.lib mse
			,d3.id_dtm id3, d3.lib fse
			,d4.id_dtm id4, d4.lib mp
			,d5.id_dtm id5, d5.lib fp
			,d6.id_dtm id6, d6.lib mpe
			,d7.id_dtm id7, d7.lib fpe
		FROM gen_determinants d0
			INNER JOIN gen_determinants d1 ON d1.id_dico = d0.id_dico AND d1.num = d0.num AND d1.ordre = 1
			INNER JOIN gen_determinants d2 ON d2.id_dico = d0.id_dico AND d2.num = d0.num AND d2.ordre = 2
			INNER JOIN gen_determinants d3 ON d3.id_dico = d0.id_dico AND d3.num = d0.num AND d3.ordre = 3
			INNER JOIN gen_determinants d4 ON d4.id_dico = d0.id_dico AND d4.num = d0.num AND d4.ordre = 4
			INNER JOIN gen_determinants d5 ON d5.id_dico = d0.id_dico AND d5.num = d0.num AND d5.ordre = 5
			INNER JOIN gen_determinants d6 ON d6.id_dico = d0.id_dico AND d6.num = d0.num AND d6.ordre = 6
			INNER JOIN gen_determinants d7 ON d7.id_dico = d0.id_dico AND d7.num = d0.num AND d7.ordre = 7
		WHERE d0.id_dico = ".$id_dico." AND d0.ordre = 0";                    
		
    	$db = $this->getAdapter()->query($sql);

		return $db->fetchAll();
    }
    	/**
     * Recherche une entrée Gen_determinants avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $num
     *
     * @return array
     */
    public function findByNum($num)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_determinants") )                           
                    ->where( "g.num = ?", $num );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_determinants avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $ordre
     *
     * @return array
     */
    public function findByOrdre($ordre)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_determinants") )                           
                    ->where( "g.ordre = ?", $ordre );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_determinants avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $lib
     *
     * @return array
     */
    public function findByLib($lib)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_determinants") )                           
                    ->where( "g.lib = ?", $lib );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
