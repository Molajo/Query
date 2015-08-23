<?php
namespace Molajo\Model;

use CommonApi\Query\DeleteModelInterface;

/**
 * As instructed by the Delete Controller. the Delete Model uses model registry data to prepare
 * data, create and run SQL statements needed to delete data.
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class DeleteModel extends Model implements DeleteModelInterface
{
    /**
     * delete - deletes a row from a table
     *
     * @param   $data
     * @param   $model_registry
     *
     * @return bool
     * @since   1.0.0
     */
    public function delete($data, $model_registry)
    {
        $table_name     = $this->registry->get($model_registry, 'table_name');
        $primary_prefix = $this->registry->get($model_registry, 'primary_prefix');
        $name_key       = $this->registry->get($model_registry, 'name_key');
        $primary_key    = $this->registry->get($model_registry, 'primary_key');

        /** Build Delete Statement */
        $deleteSQL = 'DELETE FROM ' . $table_name;

        if (isset($data->$primary_key)) {
            $deleteSQL .= ' WHERE ' . $primary_key . ' = ' . (int)$data->$primary_key;
        } elseif (isset($data->$name_key)) {
            $deleteSQL .= ' WHERE ' . $name_key . ' = ' . (
                $data->$name_key
                );
        } else {
            //only 1 row at a time with primary title or id key
            return false;
        }

        if (isset($data->catalog_type_id)) {
            $deleteSQL .= ' AND ' .
                'catalog_type_id'
                . ' = '
                . (int)$data->catalog_type_id;
        }

        $sql = $deleteSQL;

        $this->database->setQuery($sql);

        $this->database->execute($sql);

        return true;
    }
}
