<?php
/**
 * Mysqli Database Adapter Test
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

include __DIR__ . '/Bootstrap.php';

$options             = array();
$options['host']     = '127.0.0.1';
$options['port']     = '3306';
$options['user']     = 'root';
$options['password'] = 'root';
$options['database'] = 'molajo_site2';
$options['prefix']   = 'molajo_';

/**
 *  Test 1: Connect to Database
 */

try {
    $class   = 'Molajo\\Data\\Adapter\\Mysqli';
    $adapter = new $class($options);

    $class  = 'Molajo\\Data\\Driver';
    $driver = new $class($adapter);

    $driver->connect();

} catch (\Exception $e) {
    echo 'Test 1: Connection failed: ' . $e->getMessage();
}


/**
 *  Test 2: Test Escape and Escape Name functions
 */

$sql = 'select ';
$sql .= $driver->escapeName('username');
$sql .= ' from ';
$sql .= $driver->escapeName('molajo_users');
$sql .= ' where ';
$sql .= $driver->escapeName('id');
$sql .= ' = ';
$sql .= $driver->escape(1);

if ($sql === 'select `username` from `molajo_users` where `id` = 1') {
} else {
    echo 'Test 2: Escape and Escape Name Functions FAILED<br>';
}

/**
 *  Test 3: Test loadResult
 */

$value = $driver->loadResult($sql);

if ($value === 'admin') {
} else {
    echo 'Test 3: Load Result: FAILED<br>';
}

/**
 *  Test 4: Test loadObjectList
 */

$sql = 'select * ';
$sql .= ' from ';
$sql .= $driver->escapeName('molajo_users');

$results = $adapter->loadObjectList($sql);

if (count($results) === 8) {
} else {
    echo 'Test 4: Test loadObjectList FAILED<br>';
}

/**
 *  Test 5: Disconnect
 */

try {
    $driver->disconnect();
} catch (\Exception $e) {
    echo 'Test 5: Disconnect failed: ' . $e->getMessage();
}

/**
 *  Test 6: Reconnect and Run Query
 */

if ($driver->loadResult('select `username` from `molajo_users` where `id` = 1') === 'admin') {
} else {
    echo 'Test 6: Load Result: FAILED<br>';
}


/**
 *  Test 7: Execute Query
 */

if ($driver->execute('select `username` from `molajo_users` where `id` = 1')->num_rows === 1) {
} else {
    echo 'Test 7: Load Result: FAILED<br>';
}
