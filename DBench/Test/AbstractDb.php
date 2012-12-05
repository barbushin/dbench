<?php

/**
 * @see http://code.google.com/p/dbench
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */

abstract class DBench_Test_AbstractDb extends DBench_Test_Abstract {
	
	protected $db;

	public function __construct(DBench_Db_Abstract $db) {
		$this->db = $db;
	}

	public function setQuery($query) {
		$concatObjects = func_num_args() > 1 ? array_slice(func_get_args(), 1) : array ();
		$querySubstrings = explode('?', $query);
		
		if(count($concatObjects) != count($querySubstrings) - 1) {
			throw new Exception('Count of ? and replacers mismatch');
		}
		
		foreach($querySubstrings as $i => $querySubstring) {
			$this->queryBuildArray[] = $querySubstring;
			if(isset($concatObjects[$i])) {
				$this->queryBuildArray[] = $concatObjects[$i];
			}
		}
	}

	protected function buildQuery() {
		if(!$this->queryBuildArray) {
			throw new Exception('Test query is empty');
		}
		$query = '';
		foreach($this->queryBuildArray as $i => $subQuery) {
			if($i % 2) {
				$subQuery = $this->db->quote($subQuery);
			}
			$query .= $subQuery;
		}
		return $query;
	}
}