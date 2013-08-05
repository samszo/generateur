<?php
/**
 * Ce fichier contient la classe Gen_concepts.
 *
 * @copyright  2013 Samuel Szoniecky
 * @license    "New" BSD License
 */

class Model_DbTable_Gen_concepts extends Zend_Db_Table_Abstract
{
    
    /**
     * Nom de la table.
     */
    protected $_name = 'gen_concepts';
    
    /**
     * Clef primaire de la table.
     */
    protected $_primary = 'id_concept';

    protected $_referenceMap    = array(
        'Dicos' => array(
            'columns'           => 'id_dico',
            'refTableClass'     => 'Model_DbTable_Gen_dicos',
            'refColumns'        => 'id_dico'
        )
    );	

    protected $_dependentTables = array(
       "Model_DbTable_Gen_conceptsxadjectifs"
       ,"Model_DbTable_Gen_conceptsxgenerateurs"
       ,"Model_DbTable_Gen_conceptsxsubstantifs"
       ,"Model_DbTable_Gen_conceptsxsyntagmes"
       ,"Model_DbTable_Gen_conceptsxverbes"
       );    
    
    /**
     * Vérifie si une entrée est utilisée.
     *
     * @param int 		$idCpt
     * @param string	$val
     *
     * @return array
     */
    public function utilise($idCpt, $val)
    {
    	/**TODO TROP GOURMAND : optimiser en indexant les generateur par les identifiants qui les composent
    	 *  
    	 */
    	$sql ="SELECT COUNT(DISTINCT g.id_gen) nbGen
    		, COUNT(DISTINCT oduA.id_oeu) nbOeu
			, COUNT(DISTINCT g.id_dico) nbDico
			, COUNT(DISTINCT oduA.uti_id) nbUti
			, GROUP_CONCAT(DISTINCT oduA.uti_id) idsUti
		FROM gen_concepts c
			INNER JOIN gen_oeuvres_dicos_utis odu ON odu.id_dico = c.id_dico
			INNER JOIN gen_oeuvres_dicos_utis oduA ON oduA.id_oeu = odu.id_oeu
			LEFT JOIN gen_generateurs g ON g.valeur LIKE '%".$val."%' AND g.id_dico = oduA.id_dico
			WHERE c.id_concept = ".$idCpt."
			GROUP BY c.id_concept";
		$db = $this->getAdapter()->query($sql);
        return $db->fetchAll();

    } 
    
    /**
     * Vérifie si une entrée Gen_concepts existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_concept'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_concept; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gen_concepts.
     *
     * @param array $data
     * @param boolean $existe
     * @param boolean $obj
     *  
     * @return integer
     */
    public function ajouter($data, $existe=true, $obj=false)
    {
    	
    	$id=false;
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	if($obj){
    		$arr = $this->findById_concept($id);
    		return $arr[0];
    	}else
    		return $id;
    } 
           
    /**
     * Recherche une entrée Gen_concepts avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
   	
    	$this->update($data, 'gen_concepts.id_concept = ' . $id);
    }
    
    /**
     * Recherche une entrée Gen_concepts avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
		//suppression des données lieés
        $dt = $this->getDependentTables();
        foreach($dt as $t){
        	$dbT = new $t($this->_db);
        	$dbT->remove($id);
        }        
    	$this->delete('gen_concepts.id_concept = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gen_concepts avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
   	
    	$query = $this->select()
                    ->from( array("gen_concepts" => "gen_concepts") );
                    
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
     * Recherche une entrée Gen_concepts avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_concept
     *
     * @return array
     */
    public function findById_concept($id_concept)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_concepts") )                           
                    ->where( "g.id_concept = ?", $id_concept );

        return $this->fetchAll($query)->toArray(); 
    }
    /**
     * Recherche une entrée Gen_concepts avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param string $idsDicos
     *
     * @return array
     */
    public function findByIdDico($idsDicos)
    {
        $query = $this->select()
			->from( array("g" => "gen_concepts") )                           
            ->where( "g.id_dico IN (".$idsDicos.")" )
            ->order("lib");

        $rows = $this->fetchAll($query)->toArray();
        return $rows; 
    }

    /**
     * Recherche toutes les entrées conceptuelles pour les dictionnaires de
     * - concepts
     * - syntagmes
     * - pronoms
     * et retourne ces entrées.
     *
     * @param array		$dicos
     * @param boolean	$merge
     *
     * @return array
     */
    public function findAllByDicos($dicos, $merge=true)
    {

        //récupère les concepts
    	$sql ="SELECT id_concept as id
    		, lib
			, type
			, 0 as num 
    		, '' as ordre	  
		FROM gen_concepts c
		WHERE c.id_dico IN (".$dicos["concepts"].")";
		$smtp = $this->_db->query($sql);
        $concepts = $smtp->fetchAll();

        //récupère les syntagmes
    	$sql ="SELECT id_syn as id
    		, lib
			, 'syntagmes' as type
			, num
    		, ordre 
		FROM gen_syntagmes 
		WHERE id_dico IN (".$dicos["syntagmes"].")";
		$smtp = $this->_db->query($sql);
        $syns = $smtp->fetchAll();

        //récupère les pronoms
    	$sql ="SELECT id_pronom as id
    		, lib
			, CONCAT('pronom ',type) as type
			, num 
    		, '' as ordre	  
		FROM gen_pronoms 
		WHERE id_dico IN (".$dicos["pronoms"].") AND type='complément'";
		$smtp = $this->_db->query($sql);
        $prosComp = $smtp->fetchAll();

    	$sql ="SELECT id_pronom as id
    		, lib
			, CONCAT('pronom ',type) as type
			, num 
    		, '' as ordre	  
		FROM gen_pronoms 
		WHERE id_dico IN (".$dicos["pronoms"].") AND type='sujet'";
		$smtp = $this->_db->query($sql);
        $prosSujet = $smtp->fetchAll();

    	$sql ="SELECT id_pronom as id
    		, lib
			, CONCAT('pronom ',type) as type
			, num 
    		, '' as ordre	  
		FROM gen_pronoms 
		WHERE id_dico IN (".$dicos["pronoms"].") AND type='sujet_indefini'";
		$smtp = $this->_db->query($sql);
        $prosSujetInd = $smtp->fetchAll();

        //récupère les déterminants
    	$sql ="SELECT id_dtm as id
    		, lib
			, 'déterminant' as type
			, num 
    		, ordre	
		FROM gen_determinants 
		WHERE id_dico IN (".$dicos["déterminants"].")";
		$smtp = $this->_db->query($sql);
        $dets = $smtp->fetchAll();
        
        //récupère les négations
    	$sql ="SELECT id_negation as id
    		, lib
			, 'négation' as type
			, num
    		, '' as ordre	  
		FROM gen_negations 
		WHERE id_dico IN (".$dicos["negations"].")";
		$smtp = $this->_db->query($sql);
        $negs = $smtp->fetchAll();
        $nb = count($concepts)+count($syns)+count($pros)+count($dets)+count($negs);
        if($merge)
	        $rows = array_merge($concepts, $syns, $prosComp, $prosSujet, $prosSujetInd, $dets, $negs);
    	else
    		$rows = array("concepts"=>$concepts,"syntagmes"=>$syns,"pronomsComp"=>$prosComp, "pronomsSujet"=>$prosSujet, "pronomsSujetInd"=>$prosSujetInd,"determinants"=>$dets,"negations"=>$negs);
    		
        return $rows; 
    }
    
    /**
     * Recherche une entrée Gen_concepts avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $lib
     *
     * @return array
     */
    public function findByLib($lib)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_concepts") )                           
                    ->where( "g.lib = ?", $lib );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gen_concepts avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $type
     *
     * @return array
     */
    public function findByType($type)
    {
        $query = $this->select()
                    ->from( array("g" => "gen_concepts") )                           
                    ->where( "g.type = ?", $type );

        return $this->fetchAll($query)->toArray(); 
    }
    
    /**
     * Recherche les entrées Gen_concepts pour diffuser une oeurvre
     *
     * @param int $idDico
     *
     * @return array
     */
    public function getCptForDiffusion($idDico)
    {
        $query = $this->select()
        	->from( array("c" => "gen_concepts") )                           
        ->where( "c.id_dico = ?", $idDico)
        ->where( "c.type NOT IN ('a','m','s','v')")
        ->order("c.lib");
    	
        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
