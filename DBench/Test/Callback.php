<?php

/**
 * @see https://github.com/barbushin/dbench
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 * 
 */

class DBench_Test_Callback extends DBench_Test_Abstract {
	
	protected $testCallback;
	protected $preTestCallback;
	protected $postTestCallback;

	public function __construct($testCallback, $preTestCallback = null, $postTestCallback = null) {
		if(!is_callable($testCallback)) {
			throw new Exception('Test callback is not callable');
		}
		$this->testCallback = $testCallback;
		
		if($preTestCallback) {
			if(!is_callable($preTestCallback)) {
				throw new Exception('Test callback is not callable');
			}
			$this->preTestCallback = $preTestCallback;
		}
		
		if($postTestCallback) {
			if(!is_callable($postTestCallback)) {
				throw new Exception('Test callback is not callable');
			}
			$this->postTestCallback = $postTestCallback;
		}
	}

	protected function preTest() {
		if($this->preTestCallback) {
			call_user_func($this->preTestCallback);
		}
	}

	protected function postTest() {
		if($this->postTestCallback) {
			call_user_func($this->postTestCallback);
		}
	}

	protected function test() {
		if($this->testCallback) {
			call_user_func($this->testCallback);
		}
	}
}