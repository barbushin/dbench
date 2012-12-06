<?php

/**
 * @see https://github.com/barbushin/dbench
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 * 
 */

class DBench_Random_List extends DBench_Random_Abstract {
	
	protected $list;
	protected $listTopIndex;
	protected $concatCount;
	protected $separator;

	public function __construct(array $list, $concatCount=1, $separator = ' ') {
		$this->list = array_values($list);
		$this->listTopIndex = count($this->list) - 1;
		$this->concatCount = $concatCount;
		$this->separator = $separator;
	}

	public function getValue() {
		if($this->concatCount == 1) {
		return $this->list[array_rand($this->list)];
		}
		else {
			$concatCount = mt_rand(1, $this->concatCount-1);
			$string = $this->list[mt_rand(0, $this->listTopIndex)];
			for(; $concatCount; $concatCount--) {
				$string .= $this->separator.$this->list[mt_rand(0, $this->listTopIndex)];
			}
			return $string;
		}
	}
}