<?php

/**
 * @see https://github.com/barbushin/dbench
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 * 
 */

abstract class DBench_Test_Abstract {

	public function run() {
		$this->preTest();
		$start = microtime(true);
		$this->test();
		$time = microtime(true) - $start;
		$this->postTest();
		return $time;
	}

	abstract protected function test();

	protected function preTest() {
	}

	protected function postTest() {
	}
}