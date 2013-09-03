<?php
/**
 * Ce fichier contient la classe Gen_dicos.
 *
 * @copyright  2013 Samuel Szoniecky
 * @license    "New" BSD License
 */

class Model_DbTable_Gen_dicos extends Zend_Db_Table_Abstract
{
    
    /**
     * Nom de la table.
     */
    protected $_name = 'gen_dicos';
    
    /**
     * Clef primaire de la table.
     */
    protected $_primary = 'id_dico';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gen_dicos existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_dico'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_dico; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gen_dicos.
     *
     * @param array $data
     * @param int $idUti
     * @param boolean $existe
     *  
     * @return integer
     */
    public function ajouter($data, $idUti, $existe=true)
    {
    	
    	$id=false;
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	//vérifie s'il faut créer le lien avec l'utilisateur
    	if($idUti){
    		$dbDUR = new Model_DbTable_Gen_dicosxutisxroles();
    		$dbDUR->ajouter(array("id_dico"=>$id,"uti_id"=>$idUti,"id_role"=>4));
    	}
    	
    	return $id;
    } 
           
    /**
     * Recherche une entrée Gen_dicos avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
   	
    	$this->update($data, 'gen_dicos.id_dico = ' . $id);
    }
    
    /**
     * Recherche une entrée Gen_dicos avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
    	$this->delete('gen_dicos.id_dico = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gen_dicos avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
   	
    	$query = $this->select()
                    ->from( array("gen_dicos" => "gen_dicos") );
                    
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
     * Recherche une entrée Gen_dicos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_dico
     *
     * @return array
     */
    public function findByIdDico($id_dico)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_dicos") )                           
                    ->where( "g.id_dico = ?", $id_dico );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_dicos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $nom
     *
     * @return array
     */
    public function findByNom($nom)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_dicos") )                           
                    ->where( "g.nom = ?", $nom );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_dicos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $url
     *
     * @return array
     */
    public function findByUrl($url)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_dicos") )                           
                    ->where( "g.url = ?", $url );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_dicos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $type
     *
     * @return array
     */
    public function findByType($type)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_dicos") )                           
                    ->where( "g.type = ?", $type );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_dicos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param timestamp $maj
     *
     * @return array
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_dicos") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_dicos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $url_source
     *
     * @return array
     */
    public function findByUrl_source($url_source)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_dicos") )                           
                    ->where( "g.url_source = ?", $url_source );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_dicos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $path_source
     *
     * @return array
     */
    public function findByPath_source($path_source)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_dicos") )                           
                    ->where( "g.path_source = ?", $path_source );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_dicos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $langue
     *
     * @return array
     */
    public function findByLangue($langue)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_dicos") )                           
                    ->where( "g.langue = ?", $langue );

        return $this->fetchAll($query)->toArray(); 
    }
    /**
     * retourne les différentes langues du dictiionnaire
     *
     *
     * @return array
     */
    public function getLangue()
    {
    	$query = $this->select()
	    	->distinct()
	    	->from(array('d' => 'gen_dicos'), 'langue')
    		->order('langue');
    
    	return $this->fetchAll($query)->toArray();
    }
    /**
     * retourne les différentes type de dictiionnaire
     *
     *
     * @return array
     */
    public function getType()
    {
    	$query = $this->select()
    	->distinct()
    	->from(array('d' => 'gen_dicos'), 'type')
    	->order('type');
    	 
    	return $this->fetchAll($query)->toArray();
    }    
    
    /**
     * exporte un dictionnaire au format csv
     * 
     * @param int $idDico
     *
     * @return array
     */
    public function exporter($idDico)
    {
    	$sql = "SELECT 
			c.lib as concept, c.type
			, g.valeur
			FROM gen_dicos d
			INNER JOIN gen_concepts c ON d.id_dico = c.id_dico
			INNER JOIN gen_concepts_generateurs cg ON c.id_concept = cg.id_concept
			INNER JOIN gen_generateurs g ON cg.id_gen = g.id_gen
			WHERE d.id_dico = ".$idDico;
    	 
   		$stmt = $this->_db->query($sql);
    	return $stmt->fetchAll();
    }    
    
}
