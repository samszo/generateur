<?php
/**
 * Ce fichier contient la classe Gen_substantifs.
 *
 * @copyright  2013 Samuel Szoniecky
 * @license    "New" BSD License
 */

class Model_DbTable_Gen_substantifs extends Zend_Db_Table_Abstract
{
    
    /**
     * Nom de la table.
     */
    protected $_name = 'gen_substantifs';
    
    /**
     * Clef primaire de la table.
     */
    protected $_primary = 'id_sub';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gen_substantifs existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_sub'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_sub; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gen_substantifs.
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
     * Recherche une entrée Gen_substantifs avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
   	
    	$this->update($data, 'gen_substantifs.id_sub = ' . $id);
    }
    
    /**
     * Recherche une entrée Gen_substantifs avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
    	$this->delete('gen_substantifs.id_sub = ' . $id);
    }

    /**
     * Recherche les entrées de Gen_substantifs avec la clef de lieu
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
     * Récupère toutes les entrées Gen_substantifs avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
   	
    	$query = $this->select()
                    ->from( array("gen_substantifs" => "gen_substantifs") );
                    
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
     * Recherche une entrée Gen_substantifs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_sub
     *
     * @return array
     */
    public function findById_sub($id_sub)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_substantifs") )                           
                    ->where( "g.id_sub = ?", $id_sub );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_substantifs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_dico
     *
     * @return array
     */
    public function findById_dico($id_dico)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_substantifs") )                           
                    ->where( "g.id_dico = ?", $id_dico );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_substantifs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $elision
     *
     * @return array
     */
    public function findByElision($elision)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_substantifs") )                           
                    ->where( "g.elision = ?", $elision );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_substantifs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $prefix
     *
     * @return array
     */
    public function findByPrefix($prefix)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_substantifs") )                           
                    ->where( "g.prefix = ?", $prefix );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_substantifs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $genre
     *
     * @return array
     */
    public function findByGenre($genre)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_substantifs") )                           
                    ->where( "g.genre = ?", $genre );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_substantifs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $s
     *
     * @return array
     */
    public function findByS($s)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_substantifs") )                           
                    ->where( "g.s = ?", $s );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_substantifs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $p
     *
     * @return array
     */
    public function findByP($p)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_substantifs") )                           
                    ->where( "g.p = ?", $p );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
