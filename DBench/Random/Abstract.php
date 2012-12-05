<?php

/**
 * @see http://code.google.com/p/dbench
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */

abstract class DBench_Random_Abstract extends DBench_DataSource {
	
	protected $uniqueLimit;

	public function setUniqueLimit($uniqueLimit) {
		$this->uniqueLimit = $uniqueLimit;
	}
}