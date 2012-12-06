<?php

/**
 * @see https://github.com/barbushin/dbench
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 * 
 */

abstract class DBench_Random_Abstract extends DBench_DataSource {
	
	protected $uniqueLimit;

	public function setUniqueLimit($uniqueLimit) {
		$this->uniqueLimit = $uniqueLimit;
	}
}