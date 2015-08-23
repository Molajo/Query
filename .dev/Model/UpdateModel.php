<?php
namespace Molajo\Model;

use CommonApi\Query\UpdateModelInterface;

/**
 * As instructed by the Update Controller. the Update Model uses model registry data to prepare
 * data, Update and run SQL statements needed to Update data
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class UpdateModel extends Model implements UpdateModelInterface
{
    /**
     * Update - inserts a new row into a table
     *
     * @param   string $data
     *
     * @return  object
     * @since   1.0.0
     */
    public function Update($data, $model_registry)
    {
        $table_name     = $this->registry->get($model_registry, 'table_name');
        $primary_prefix = $this->registry->get($model_registry, 'primary_prefix');

        /** Prepare Data from Custom Field Groups */
        $customfieldgroups = $this->registry->get(
            $model_registry,
            'customfieldgroups',
            array()
        );

        if (is_array($customfieldgroups) && count($customfieldgroups) > 0) {

            foreach ($customfieldgroups as $customFieldName) {

                /** For this Custom Field Group (ex. Parameters, metadata, etc.) */
                $customFieldName = strtolower($customFieldName);

                /** Retrieve Field Definitions from Registry (XML) */
                $fields = $this->registry->get($model_registry, $customFieldName);

                $temp = $data->$customFieldName;

                /** Shared processing  */
                foreach ($fields as $field) {

                    $name = $field['name'];
                    $type = $field['type'];

                    if (isset($field['identity'])) {
                        $identity = $field['identity'];
                    } else {
                        $identity = 0;
                    }
                    if ($identity === 1) {
                        $type = 'identity';
                    }

                    $value = $this->prepareFieldValues($type, $temp[$name]);
                    if ($value === false) {
                        $valid = false;
                        break;
                    }

                    /** data element for SQL insert */
                    $data->$customFieldName[$name] = $value;
                }
            }
        }

        /** Build Insert Statement */
        $fields = $this->registry->get($model_registry, 'Fields');

        $insertSQL = 'INSERT INTO '
            . $table_name
            . ' ( ';
        $valuesSQL = ' VALUES ( ';

        $first = true;

        foreach ($fields as $f) {

            if ($first === true) {
                $first = false;
            } else {
                $insertSQL .= ', ';
                $valuesSQL .= ', ';
            }

            $name = $f['name'];
            $type = strtolower($f['type']);

            $insertSQL .= $name;

            $valuesSQL .= $this->prepareFieldValues($type, $data->$name);
        }

        $sql = $insertSQL . ') ' . $valuesSQL . ') ';

        $this->database->setQuery($sql);
        $this->database->execute($sql);

        $id = $this->database->insertid();

        return $id;
    }

    /**
     * prepareFieldValues prepares the values of each data element for insert into the database
     *
     * @param     $name
     * @param     $type
     * @param     $data
     *
     * @return string - data element value
     * @since  1.0
     */
    protected function prepareFieldValues($type, $input)
    {
        $value = '';

        if ($type === 'identity') {
            $value = 'null';
        } elseif ($input === null) {
            $value = 'null';
        } elseif ($type === 'integer'
            || $type === 'binary'
            || $type === 'catalog_id'
            || $type === 'boolean'
        ) {

            $value = (int)$input;
        } elseif ($type === 'string'
            || $type === 'datetime'
            || $type === 'url'
            || $type === 'email'
            || $type === 'text'
            || $type === 'ip_address'
        ) {

            $value = ($input);
        } elseif ($type === 'password') {
        } elseif ($type === 'customfield') {
            $value = (json_encode($input));
        } else {
            echo 'UNKNOWN TYPE ' . $type . ' in UpdateModel::prepareFieldValues <br />';
        }

        return $value;
    }
}
