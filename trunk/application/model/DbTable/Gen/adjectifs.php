<?php
/**
 * Ce fichier contient la classe Gen_adjectifs.
 *
 * @copyright  2013 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gen_adjectifs extends Zend_Db_Table_Abstract
{
    
    /**
     * Nom de la table.
     */
    protected $_name = 'gen_adjectifs';
    
    /**
     * Clef primaire de la table.
     */
    protected $_primary = 'id_adj';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gen_adjectifs existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_adj'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_adj; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gen_adjectifs.
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
     * Recherche une entrée Gen_adjectifs avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
   	
    	$this->update($data, 'gen_adjectifs.id_adj = ' . $id);
    }
    
    /**
     * Recherche une entrée Gen_adjectifs avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
    	$this->delete('gen_adjectifs.id_adj = ' . $id);
    }

    /**
     * Recherche les entrées de Gen_adjectifs avec la clef de lieu
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
     * Récupère toutes les entrées Gen_adjectifs avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
   	
    	$query = $this->select()
                    ->from( array("gen_adjectifs" => "gen_adjectifs") );
                    
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
     * Recherche une entrée Gen_adjectifs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_adj
     *
     * @return array
     */
    public function findById_adj($id_adj)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_adjectifs") )                           
                    ->where( "g.id_adj = ?", $id_adj );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_adjectifs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_dico
     *
     * @return array
     */
    public function findById_dico($id_dico)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_adjectifs") )                           
                    ->where( "g.id_dico = ?", $id_dico );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_adjectifs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $elision
     *
     * @return array
     */
    public function findByElision($elision)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_adjectifs") )                           
                    ->where( "g.elision = ?", $elision );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_adjectifs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $prefix
     *
     * @return array
     */
    public function findByPrefix($prefix)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_adjectifs") )                           
                    ->where( "g.prefix = ?", $prefix );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_adjectifs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $m_s
     *
     * @return array
     */
    public function findByM_s($m_s)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_adjectifs") )                           
                    ->where( "g.m_s = ?", $m_s );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_adjectifs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $f_s
     *
     * @return array
     */
    public function findByF_s($f_s)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_adjectifs") )                           
                    ->where( "g.f_s = ?", $f_s );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_adjectifs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $m_p
     *
     * @return array
     */
    public function findByM_p($m_p)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_adjectifs") )                           
                    ->where( "g.m_p = ?", $m_p );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_adjectifs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $f_p
     *
     * @return array
     */
    public function findByF_p($f_p)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_adjectifs") )                           
                    ->where( "g.f_p = ?", $f_p );

        return $this->fetchAll($query)->toArray(); 
    }
    
	/**
     * Recherche une entrée Gen_adjectifs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_concept
     *
     * @return array
     */
    public function findByIdConcept($id_concept)
    {
        $query = $this->select()
        	->from( array("ca" => "gen_concepts_adjectifs") )                           
	        ->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
        ->joinInner(array('a' => 'gen_adjectifs'),
        		'a.id_adj = ca.id_adj')
        ->where("ca.id_concept = ?", $id_concept );
        
        return $this->fetchAll($query)->toArray(); 
    }
    
}
