<?php
/**
 * Query Adapter
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query;

use \DateTime;

include __DIR__ . '/Bootstrap.php';

$options = array();

$options['db_type']        = 'MySQLi';
$options['db_host']        = 'localhost';
$options['db_user']        = 'root';
$options['db_password']    = 'root';
$options['db_name']        = 'molajo';
$options['db_prefix']      = 'molajo_';
$options['process_events'] = 1;
$options['select']         = true;

use Molajo\Query\Handler\Joomla;

$adapter_handler = new Joomla($options);
$adapter         = new Adapter($adapter_handler);

$text = "Escape ' this text";
echo '<br />' . $adapter->escape($text);

$value = "Quote these words";
echo '<br />' . $adapter->quote($value);

$value = 'Display this date format: ';
echo '<br />' . $value . $adapter->getDateFormat();

$date = new DateTime();
echo 'Formatted Date Now: ' . $date->format($adapter->getDateFormat());

$value = 'Display this null date: ';
echo '<br />' . $value . $adapter->getNullDate();

echo '<br /> Get the Query Object, fill the query Return the result <br />';
$query = $adapter->getQueryObject();

$query->select('username')->from('molajo_users')->where('id = 1');
echo $adapter->loadResult();

echo '<br /> Get the Query Object, fill the query Load the results <br />';
$query = $adapter->getQueryObject();
$query->select('*')->from('molajo_users');
$results = $adapter->loadObjectList();

echo '<pre>';
var_dump($results);
echo '</pre>';
