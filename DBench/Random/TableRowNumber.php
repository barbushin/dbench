<?php

/**
 * @see http://code.google.com/p/dbench
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */

class DBench_Random_TableRowNumber extends DBench_Random_Abstract {
	
	protected $tableBuilder;
	protected $byLastBuilt;

	public function __construct(DBench_TableDataGenerator $tableBuilder, $byLastBuilt = false) {
		$this->tableBuilder = $tableBuilder;
		$this->byLastBuilt = $byLastBuilt;
	}

	public function getValue() {
		if($this->byLastBuilt) {
			return mt_rand($this->tableBuilder->getLastBuiltRowsNumber() + 1, $this->tableBuilder->getBuiltRowsNumber());
		}
		else {
			return mt_rand(1, $this->tableBuilder->getBuiltRowsNumber());
		}
	}
}