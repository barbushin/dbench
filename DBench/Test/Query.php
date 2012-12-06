<?php

/**
 * @see https://github.com/barbushin/dbench
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 * 
 */

class DBench_Test_Query extends DBench_Test_AbstractDb {
	
	protected $currentTestSql;

	protected function preTest() {
		$this->currentTestSql = $this->buildQuery();
	}

	protected function test() {
		$this->db->query($this->currentTestSql);
	}
}