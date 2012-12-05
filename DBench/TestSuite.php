<?php

/**
 * @see http://code.google.com/p/dbench
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */

class DBench_TestSuite {
	
	protected $tests = array ();
	protected $lastTimeResults = array ();
	protected $debugCallback;
	
	const DEFAULT_DELAY_MICROSEC = 0;
	const TIMES_AVG_DEVIDE_INDEX = 6;

	public function runTests() {
		$this->lastTimeResults = array ();
		foreach($this->tests as $name => $test) {
			$this->debug('Run test "' . $name . '"');
			$this->lastTimeResults[$name] = $this->runTest($test['object'], $test['repeat'], $test['delay']);
		}
	}

	protected function runTest(DBench_Test_Abstract $test, $repeat, $delayMicrosec = self::DEFAULT_DELAY_MICROSEC) {
		$times = array ();
		for($i = 0; $i < $repeat; $i++) {
			usleep($delayMicrosec);
			$times[] = $test->run();
		}
		return $this->getAvgTime($times);
	}

	protected function getAvgTime(array $times) {
		sort($times);
		if(count($times) < 6) {
			return array_sum($times) / count($times);
		}
		else {
			$timesDiff = array ();
			$diffLength = ceil(count($times) / self::TIMES_AVG_DEVIDE_INDEX);
			foreach($times as $i => $time) {
				if($i >= $diffLength && $i < count($times) - $diffLength) {
					$timesDiff[$i] = 0;
					for($d = $i - $diffLength; $d <= $i + $diffLength; $d++) {
						$timesDiff[$i] += abs($time - $times[$d]);
					}
				}
			}
			asort($timesDiff);
			foreach($timesDiff as $i => $diff) {
				return round(($times[$i]) * 1000, 3);
			}
		}
	}

	public function setDebugCallback($debugCallback) {
		if(!is_callable($debugCallback)) {
			throw new Exception('Callback is not callable');
		}
		$this->debugCallback = $debugCallback;
	}

	protected function debug($message) {
		if($this->debugCallback) {
			call_user_func($this->debugCallback, $message);
		}
	}

	public function getLastTimeResults() {
		return $this->lastTimeResults;
	}

	public function addTest($name, DBench_Test_Abstract $testObject, $repeat = 1, $delayMicrosec = self::DEFAULT_DELAY_MICROSEC) {
		if(isset($this->tests[$name])) {
			throw new Exception('Test with name "' . $name . '" already added to test suite');
		}
		$test['object'] = $testObject;
		$test['repeat'] = $repeat;
		$test['delay'] = $delayMicrosec;
		
		$this->tests[$name] = $test;
	}
}