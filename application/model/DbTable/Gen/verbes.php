<?php
/**
 * Ce fichier contient la classe Gen_verbes.
 *
 * @copyright  2013 Samuel Szoniecky
 * @license    "New" BSD License
 */

class Model_DbTable_Gen_verbes extends Zend_Db_Table_Abstract
{
    
    /**
     * Nom de la table.
     */
    protected $_name = 'gen_verbes';
    
    /**
     * Clef primaire de la table.
     */
    protected $_primary = 'id_verbe';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gen_verbes existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_verbe'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_verbe; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gen_verbes.
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
     * Recherche une entrée Gen_verbes avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
   	
    	$this->update($data, 'gen_verbes.id_verbe = ' . $id);
    }
    
    /**
     * Recherche une entrée Gen_verbes avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
    	$this->delete('gen_verbes.id_verbe = ' . $id);
    }

    /**
     * Recherche les entrées de Gen_verbes avec la clef de lieu
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
     * Récupère toutes les entrées Gen_verbes avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
   	
    	$query = $this->select()
                    ->from( array("gen_verbes" => "gen_verbes") );
                    
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
     * Recherche une entrée Gen_verbes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_verbe
     *
     * @return array
     */
    public function findById_verbe($id_verbe)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_verbes") )                           
                    ->where( "g.id_verbe = ?", $id_verbe );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_verbes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_conj
     *
     * @return array
     */
    public function findById_conj($id_conj)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_verbes") )                           
                    ->where( "g.id_conj = ?", $id_conj );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_verbes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_dico
     *
     * @return array
     */
    public function findById_dico($id_dico)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_verbes") )                           
                    ->where( "g.id_dico = ?", $id_dico );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_verbes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $elision
     *
     * @return array
     */
    public function findByElision($elision)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_verbes") )                           
                    ->where( "g.elision = ?", $elision );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_verbes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $prefix
     *
     * @return array
     */
    public function findByPrefix($prefix)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_verbes") )                           
                    ->where( "g.prefix = ?", $prefix );

        return $this->fetchAll($query)->toArray(); 
    }
    
	/**
     * Recherche une entrée Gen_verbes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_concept
     *
     * @return array
     */
    public function findByIdConcept($id_concept)
    {
        $query = $this->select()
        	->from( array("cv" => "gen_concepts_verbes") )                           
	        ->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
        ->joinInner(array('v' => 'gen_verbes'), 'v.id_verbe = cv.id_verbe')
        ->joinInner(array('c' => 'gen_conjugaisons'), 'c.id_conj = v.id_conj')
        ->joinInner(array('dc' => 'gen_dicos'), 'dc.id_dico = c.id_dico', array("dicoConj"=>"nom"))
        ->where("cv.id_concept = ?", $id_concept );
        
        return $this->fetchAll($query)->toArray(); 
    }
    
}
