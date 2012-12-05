<?php

/**
 * @see http://code.google.com/p/dbench
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */

class DBench_Test_Fetch extends DBench_Test_AbstractDb {
	
	protected $currentTestQueryResult;

	protected function preTest() {
		$this->currentTestQueryResult = $this->db->query($this->buildQuery());
	}

	protected function test() {
		$this->db->fetchResultToArray($this->currentTestQueryResult);
	}
}