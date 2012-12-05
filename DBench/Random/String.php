<?php

/**
 * @see http://code.google.com/p/dbench
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */

class DBench_Random_String extends DBench_Random_Abstract {
	
	protected $minLength;
	protected $maxLength;

	public function __construct($minLength = 0, $maxLength = 255) {
		$this->minLength = $minLength;
		$this->maxLength = $maxLength;
	}

	public function getValue() {
		if($this->uniqueLimit) {
			$uid = mt_rand(0, $this->uniqueLimit);
			$substr = md5($uid);
			$length = $this->minLength + ($uid % ($this->maxLength - $this->minLength));
		
		}
		else {
			$substr = md5(mt_rand());
//			$length = mt_rand($this->minLength, $this->maxLength);
		}
		return $substr;
//		return substr(str_repeat($substr, ceil($length / strlen($substr))), 0, $length);
	}
}
