<?php

/**
 * @see http://code.google.com/p/dbench
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */

class DBench_Random_Bool extends DBench_Random_Number {

	public function __construct() {
		$this->min = 0;
		$this->max = 1;
	}
}
