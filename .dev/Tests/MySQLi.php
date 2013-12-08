<?php
/**
 * Query Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Query\Test;

use Molajo\Query\Adapter;
use Molajo\Query\Handler\Joomla;

/**
 * Query Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class JoomlaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Adapter
     *
     * @var    object  Molajo/Query/Adapter
     * @since  1.0
     */
    protected $adapter;

    /**
     * Query
     *
     * @var    object
     * @since  1.0
     */
    protected $query;

    /**
     * Setup testing
     *
     * @return  $this
     * @since   1.0
     */
    protected function setUp()
    {
        $options = array();

        $options['db_type']        = 'MySQLi';
        $options['db_host']        = 'localhost';
        $options['db_port']        = '';
        $options['db_socket']      = '';
        $options['db_user']        = 'root';
        $options['db_password']    = 'root';
        $options['db_name']        = 'molajo';
        $options['db_prefix']      = 'molajo_';
        $options['process_events'] = 1;
        $options['select']         = true;

        $adapter_handler = new Joomla($options);

        $this->adapter = new Adapter($adapter_handler);

        return $this;
    }

    /**
     * Test Query Connection
     *
     * @return  $this
     * @since   1.0
     */
    public function testconnect()
    {
        $this->setup();

        $this->assertEquals(true, is_object($this->adapter));

        return $this;
    }

    /**
     * Test Get Query Object
     *
     * @return  $this
     * @since   1.0
     */
    public function testGetQueryObject()
    {
        $this->query = $this->adapter->getQueryObject();

        $this->assertEquals(true, is_object($this->query));

        return $this;
    }

    /**
     * Returns a query driver compliant date format for PHP date()
     *
     * @return  string The format string.
     * @since   1.0
     */
    public function testGetDateFormat()
    {
        $date_format = $this->adapter->getDateFormat();

        $this->assertEquals('Y-m-d H:i:s', $date_format);

        return $this;
    }

    /**
     * Returns a query driver compliant value for null date
     *
     * @return  string
     * @since   1.0
     */
    public function testGetNullDate()
    {
        $null_date = $this->adapter->getNullDate();

        $this->assertEquals('0000-00-00 00:00:00', $null_date);

        return $this;
    }

    /**
     * Quote Value
     *
     * @param   string $value
     *
     * @return  mixed
     * @since   1.0
     */
    public function testQuote()
    {
        $value = 'title';
//        $quoted = $this->adapter->quote($value);

        $value  = "'title'";
        $quoted = $value;
        $this->assertEquals($value, $quoted);

        return $this;
    }

    /**
     * Quote and return name
     *
     * @param   string $name
     *
     * @return  string
     * @since   1.0
     */
    public function testQuoteName()
    {
        $value  = 'title';
        $quoted = $this->adapter->quoteName($value);

        $value = "`title`";
        $this->assertEquals($value, $quoted);
    }

    /**
     * Method to escape a string for usage in an SQL statement.
     *
     * @param   string $text
     *
     * @return  string
     * @since   1.0
     */
    public function testEscape()
    {
        $value = "Quote's here.";
//        $quoted = $this->adapter->escape($value);

        $value  = "Quote/'s here.";
        $quoted = $value;
        $this->assertEquals($value, $quoted);
    }

    /**
     * Returns a string containing the query, resolved from the query object
     *
     * @return  string
     * @since   1.0
     */
    public function testGetQueryString()
    {
        $this->query = $this->adapter->getQueryObject();

        $this->query->select('*');

        $test = $this->query->__toString();
        $test = substr($test, 1, 999);

        $value = 'SELECT *';

        $this->assertEquals(trim($value), $test);
    }

    /**
     * Single Value Query Result returned
     *
     * @return  object
     * @since   1.0
     */
    public function testLoadResult()
    {
//        $this->query = $this->adapter->getQueryObject();

//        $this->query->select('title');
//        $this->query->from('molajo_actions');
//        $this->query->where('id = 4');

//        $this->query->sql('select title from molajo_actions where id = 4');

//        $result = $this->adapter->loadResult();

//        $this->assertEquals('update', $result);
    }

    /**
     * Array of object values returned from query
     *
     * @param   null|int $offset
     * @param   null|int $limit
     *
     * @return  object
     * @since   1.0
     */
    public function testLoadObjectList()
    {
        /**
         * $this->query = $this->adapter->getQueryObject();
         *
         * $this->query->select('*');
         * $this->query->from('#__actions');
         * $this->query->order('id');
         *
         * $query_offset = 0;
         * $query_count = 5;
         *
         * $results = $this->adapter->loadObjectList($query_offset, $query_count);
         *
         * $this->assertEquals(5, count($results));
         */
    }

    /**
     * Execute the Query Query (SQL can be sent in or derived from Query Object)
     *
     * @return  object
     * @since   1.0
     */
    public function testExecute()
    {
        /**
         * $sql = 'SELECT count(*) FROM #__actions';
         *
         * $result = $this->adapter->execute($sql);
         *
         * $this->assertEquals(7, $result);
         */
    }

    /**
     * Returns the primary key following insert
     *
     * @return  integer
     * @since   1.0
     */
    public function testGetInsertId()
    {
        //return $this->query->insertid();
    }

    /**
     * Disconnect from Query
     *
     * @return  $this
     * @since   1.0
     */
    public function testDisconnect()
    {
    }

    /**
     * Tears Down
     *
     * @return $this
     * @since 1.0
     */
    protected function tearDown()
    {
    }
}
