<?php

/**
 * @see https://github.com/barbushin/dbench
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
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