<?php
/**
 * Database Factory Method
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Factories\Database;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\IoC\FactoryInterface;
use CommonApi\IoC\FactoryBatchInterface;
use Molajo\IoC\FactoryMethod\Base as FactoryMethodBase;
use stdClass;

/**
 * Database Factory Method
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class DatabaseFactoryMethod extends FactoryMethodBase implements FactoryInterface, FactoryBatchInterface
{
    /**
     * Database options
     *
     * @var    array
     * @since  1.0
     */
    protected $db_options = array('type', 'host', 'port', 'user', 'password', 'database', 'prefix');

    /**
     * Database Prefix
     *
     * @var    string
     * @since  1.0
     */
    protected $prefix;

    /**
     * Constructor
     *
     * @param  $options
     *
     * @since  1.0
     */
    public function __construct(array $options = array())
    {
        $options['product_name']             = basename(__DIR__);
        $options['store_instance_indicator'] = true;
        $options['product_namespace']        = 'Molajo\\Data\\Driver';

        parent::__construct($options);
    }

    /**
     * Instantiate a new adapter and inject it into the Adapter for the FactoryInterface
     *
     * @return  array
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function setDependencies(array $reflection = array())
    {
        parent::setDependencies($reflection);

        $this->dependencies                = array();
        $this->dependencies['Resource']    = array();
        $this->dependencies['Runtimedata'] = array();

        return $this->dependencies;
    }

    /**
     * Instantiate Class
     *
     * @return  object
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function instantiateClass()
    {
        try {
            $adapter = $this->getAdapter();

            $adapter->connect();

            $this->product_result = $this->getDriver($adapter);

        } catch (Exception $e) {
            echo $e->getMessage();

            throw new RuntimeException (
                'Database Factory Method Adapter Instance Failed for ' . $this->product_namespace
                . ' failed.' . $e->getMessage()
            );
        }

        return $this;
    }

    /**
     * Follows the completion of the instantiate method
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterInstantiation()
    {
        if (file_exists($this->base_path . '/Bootstrap/Files/Model/Fields.json')) {
        } else {
            $this->getFields();
        }

        return $this;
    }

    /**
     * Get the Database specific Adapter Handler
     *
     * @param   string $options
     *
     * @return  object
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getAdapter()
    {
        $options = $this->setConnectionOptions();

        $class = 'Molajo\\Data\\Adapter\\' . ucfirst(strtolower($options['type']));

        try {
            return new $class($options);

        } catch (Exception $e) {
            throw new RuntimeException(
                'Database: Could not instantiate Database Adapter ' . $class
            );
        }
    }

    /**
     * Get Database Adapter, inject with specific Database Adapter Handler
     *
     * @param   object $adapter
     *
     * @return  object
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getDriver($adapter)
    {
        try {
            return new $this->product_namespace($adapter);
        } catch (Exception $e) {
            throw new RuntimeException (
                'Database: Could not instantiate Adapter'
            );
        }
    }

    /**
     * Set the Database Connection Values
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setConnectionOptions()
    {
        $options = array();

        foreach ($this->db_options as $key) {
            if (isset($this->dependencies['Runtimedata']->site->database->$key)) {
                $options[$key] = $this->dependencies['Runtimedata']->site->database->$key;
            } else {
                $options[$key] = null;
            }
        }

        $this->prefix = $options['prefix'];

        return $options;
    }

    /**
     * Get Field Data
     *
     * @return  $this
     * @since   1.0.0
     */
    public function getFields()
    {
        $sql = 'SELECT `title`, `customfields`                        ';
        $sql .= '   FROM `' . $this->prefix . 'extension_instances`   ';
        $sql .= '   WHERE `catalog_type_id` = 500                     ';
        $sql .= '       AND `status` = 1                              ';
        $sql .= '   ORDER BY `title`                                  ';

        $fields = $this->product_result->loadObjectList($sql);

        $field_array = array();

        foreach ($fields as $key => $value) {
            $name               = strtolower($value->title);
            $field_array[$name] = json_decode($value->customfields);
        }

        $x = json_encode($field_array, JSON_PRETTY_PRINT);

        file_put_contents($this->base_path . '/Bootstrap/Files/Model/Fields.json', $x);

        return $this;
    }
}
