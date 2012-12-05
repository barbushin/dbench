<?php

// Database

define('DB_CLASS', 'DBench_Db_MySQL'); // DBench_Db_PostgreSQL or DBench_Db_MySQL 
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'test');
define('DB_PERSISTANT', true);
define('DB_BULK_INSERT_LIMIT', 3000);

// Build settings

define('USERS_STEP_NUMBER', 1000);
define('USERS_LIMIT_NUMBER', 5000);
define('PHOTOS_PER_USER_NUMBER', 10);
define('COMMENTS_PER_PHOTO_NUMBER', 10);

// Test settings

define('TESTS_REPEATS', 100);
define('TESTS_DELAY_MICROSEC', 0);
define('TESTS_RESULT_CSV_FILEPATH', 'csv/tests_' . date('Y_m_d_H_i_s') . '.csv');
define('TESTS_RESULT_CHART_FILEPATH', 'charts/tests_chart_' . date('Y_m_d_H_i_s') . '.png');

// Autoload classes
define('DA_BENCH_ROOT_DIR', dirname(dirname(__FILE__)));
function autoloadDBenchClasses($class) {
	$filePath = DA_BENCH_ROOT_DIR . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
	if(is_file($filePath)) {
		return require_once ($filePath);
	}
}
spl_autoload_register('autoloadDBenchClasses');

ini_set('display_errors', 'on');
error_reporting(E_ALL);