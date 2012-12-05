<?php

/**
 * @see http://code.google.com/p/dbench
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */

class DBench_Random_Md5 extends DBench_Random_Abstract {
	
	protected $stringLimit;
	
	public function __construct($stringLimit=null) {
		$this->stringLimit = $stringLimit;
	}
	
	public function getValue() {
		$hash = md5(mt_rand());
		return $this->stringLimit ? substr($hash, 0, $this->stringLimit) : $hash;
	}
}