<?php
/**
 * Create Controller
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

use CommonApi\Query\CreateControllerInterface;

/**
 * Create Controller
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Create extends Initialise implements CreateControllerInterface
{
    /**
     * Set Insert Statement
     *
     * @param   object $row
     *
     * @return  $this
     * @since   1.0.0
     */
    public function setInsertStatement($row)
    {
        $this->row = $row;

        $this->triggerOnBeforeCreateEvent();

        $this->setInsertStatementTable();

        foreach ($this->query->getModelRegistry('fields') as $field) {

            $name = $field['name'];

            if (isset($field['identity']) && (int)$field['identity'] === 1) {
            } else {
                $this->setInsertStatementField($row, $name, $field['type']);
            }
        }

        return $this;
    }

    /**
     * Set Insert Statement Table Name
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setInsertStatementTable()
    {
        $this->query->clearQuery();

        $this->setType('insert');

        $table_name = $this->query->getModelRegistry('table_name');

        $this->query->setInsertInto($table_name);

        return $this;
    }

    /**
     * Set Insert Statement Field
     *
     * @param   object $row
     * @param   string $name
     * @param   string $type
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setInsertStatementField($row, $name, $type)
    {
        $value = null;

        if (isset($row->$name)) {
            $value = $row->$name;
        }

        $this->query->select($name, null, $value, $type);

        return $this;
    }

    /**
     * Method to get retrieve data
     *
     * @return  mixed
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function insertData()
    {
        $this->verifyForeignKeys();

        $this->executeQuery();

        $this->triggerOnAfterCreateEvent();

        return $this->row;
    }

    /**
     * Verify Foreign Keys
     *
     * @return  $this
     * @since   1.0.0
     */
    public function verifyForeignKeys()
    {
        return $this;
    }

    /**
     * Execute the Query
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function executeQuery()
    {
        $value = $this->model->insertData($this->sql);

        if ($value === null) {
            return $this;
        }

        $id = $this->getModelRegistry('primary_key', null);

        if ($id === null) {
            return $this;
        }

        $this->row->$id = $value;

        return $this;
    }

    /**
     * Schedule onBeforeCreate Event
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function triggerOnBeforeCreateEvent()
    {
        return $this->triggerEvent('onBeforeCreate');
    }

    /**
     * Schedule onAfterCreate Event
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function triggerOnAfterCreateEvent()
    {
        return $this->triggerEvent('onAfterCreate');
    }
}
