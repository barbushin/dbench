<?php

/**
 * @see https://github.com/barbushin/dbench
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 * 
 */

class DBench_Random_Bool extends DBench_Random_Number {

	public function __construct() {
		$this->min = 0;
		$this->max = 1;
	}
}
