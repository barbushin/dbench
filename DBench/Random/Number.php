<?php

/**
 * @see https://github.com/barbushin/dbench
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 * 
 */

class DBench_Random_Number extends DBench_Random_Abstract {
	
	protected $min;
	protected $max;

	public function __construct($min = 0, $max = 999999999) {
		$this->min = $min;
		$this->max = $max;
	}

	public function getValue() {
		return mt_rand($this->min, $this->max);
	}
}