<?php

require_once ('config.php');

function debug($message) {
	echo $message . '<br />';
	flush();
}

$dbClass = DB_CLASS;
$db = new $dbClass(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE, DB_PERSISTANT);

debug('Build tables structure');
$dbInitFiles = array ('DBench_Db_PostgreSQL' => 'init_postgres.sql', 'DBench_Db_MySQL' => 'init_mysql.sql');
foreach(explode(';', file_get_contents($dbInitFiles[DB_CLASS])) as $query) {
	if(trim($query, " \n\r")) {
		$db->query($query);
	}
}

$someWordsDictionary = file('words.dic', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$usersTable = new DBench_TableDataGenerator('users');
$usersTable->addField('login', new DBench_Random_List($someWordsDictionary, 3, ''));
$usersTable->addField('password', new DBench_Random_Md5());
$usersTable->addField('is_active', new DBench_Random_List(array (1, 1, 1, 0)));

$photosTable = new DBench_TableDataGenerator('photos');
$photosTable->addField('user_id', new DBench_Random_TableRowNumber($usersTable, true));
$photosTable->addField('file', new DBench_Random_Md5());
$photosTable->addField('name', new DBench_Random_List($someWordsDictionary, 2));
$photosTable->addDependsOnTableBuilder($usersTable);

$commentsTable = new DBench_TableDataGenerator('photos_comments');
$commentsTable->addField('photo_id', new DBench_Random_TableRowNumber($photosTable, true));
$commentsTable->addField('user_id', new DBench_Random_TableRowNumber($usersTable, true));
$commentsTable->addField('text', new DBench_Random_List($someWordsDictionary, 10));
$commentsTable->addDependsOnTableBuilder($usersTable);
$commentsTable->addDependsOnTableBuilder($photosTable);

$dbBuilder = new DBench_DbDataBuilder($db, DB_BULK_INSERT_LIMIT);
$dbBuilder->setDebugCallback('debug');
$dbBuilder->addTableBuilder($usersTable);
$dbBuilder->addTableBuilder($photosTable);
$dbBuilder->addTableBuilder($commentsTable);

$testSuite = new DBench_TestSuite();
$testSuite->setDebugCallback('debug');

$testUserById = new DBench_Test_Query($db);
$testUserById->setQuery('SELECT * FROM users WHERE id=?', new DBench_Random_TableRowNumber($usersTable));
$testSuite->addTest('getUserById', $testUserById, TESTS_REPEATS, TESTS_DELAY_MICROSEC);

$testUserByLogin = new DBench_Test_Query($db);
$testUserByLogin->setQuery('SELECT * FROM users WHERE login=?', new DBench_Random_List($someWordsDictionary, 3, ''));
$testSuite->addTest('getUserByLogin', $testUserByLogin, TESTS_REPEATS, TESTS_DELAY_MICROSEC);

$testPhotosByUserId = new DBench_Test_Query($db);
$testPhotosByUserId->setQuery('SELECT * FROM photos WHERE id=?', new DBench_Random_TableRowNumber($photosTable));
$testSuite->addTest('getPhotoById', $testPhotosByUserId, TESTS_REPEATS, TESTS_DELAY_MICROSEC);

$testPhotosByUserId = new DBench_Test_Query($db);
$testPhotosByUserId->setQuery('SELECT * FROM photos WHERE user_id=?', new DBench_Random_TableRowNumber($usersTable));
$testSuite->addTest('getPhotosByUserId', $testPhotosByUserId, TESTS_REPEATS, TESTS_DELAY_MICROSEC);

$testCommentsByPhotoId = new DBench_Test_Query($db);
$testCommentsByPhotoId->setQuery('SELECT * FROM photos_comments WHERE photo_id=?', new DBench_Random_TableRowNumber($photosTable));
$testSuite->addTest('getCommentsByPhotoId', $testCommentsByPhotoId, TESTS_REPEATS, TESTS_DELAY_MICROSEC);

$log = new DBench_Log_Csv(TESTS_RESULT_CSV_FILEPATH, "\t");

$testsResults = array ();

for($usersNumber = 0; $usersNumber < USERS_LIMIT_NUMBER; $usersNumber += USERS_STEP_NUMBER) {
	
	$usersTable->setRowsNumberToBuild(USERS_STEP_NUMBER);
	$photosTable->setRowsNumberToBuild(USERS_STEP_NUMBER * PHOTOS_PER_USER_NUMBER);
	$commentsTable->setRowsNumberToBuild(USERS_STEP_NUMBER * PHOTOS_PER_USER_NUMBER * COMMENTS_PER_PHOTO_NUMBER);
	
	debug('Build tables data for next ' . USERS_STEP_NUMBER . ' users. Total ' . $usersNumber);
	$dbBuilder->build();
	
	$testSuite->runTests();
	$testResult = $testSuite->getLastTimeResults();
	$testsResults[$usersTable->getBuiltRowsNumber()] = $testResult;
	
	$logRow = array ();
	$logRow['users'] = $usersTable->getBuiltRowsNumber();
	$logRow['photos'] = $photosTable->getBuiltRowsNumber();
	$logRow['comments'] = $commentsTable->getBuiltRowsNumber();
	$logRow = array_merge($logRow, $testResult);
	$log->logRow($logRow);
}

debug('Save tests results in CSV <a href="' . TESTS_RESULT_CSV_FILEPATH . '">file</a>');
$log->save();

debug('Bulid tests results chart image');
$chart = new DBench_Log_Chart(USERS_STEP_NUMBER, USERS_LIMIT_NUMBER, USERS_STEP_NUMBER, TESTS_RESULT_CHART_FILEPATH);
$chart->chartTitle = 'Graphic of SQL requests time depends on users count. Every user has ' . PHOTOS_PER_USER_NUMBER . ' photos. Every photo has ' . (PHOTOS_PER_USER_NUMBER * COMMENTS_PER_PHOTO_NUMBER) . ' comments.';
$chartLines = array ();
$absciseLines = array ();
foreach($testsResults as $usersNumber => $testResult) {
	foreach($testResult as $testName => $result) {
		$chartLines[$testName][] = $result;
	}
	$absciseLines[] = ($usersNumber / 1000) . 'k';
}

$chart->setAbsciseValues($absciseLines);

foreach($chartLines as $name => $results) {
	$chart->addLine($name, $results);
}
$chart->build();

debug('<img src="' . TESTS_RESULT_CHART_FILEPATH . '" />');

debug('Finish');