<?php
/**
 * Datalist Controller
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Controller;

use CommonApi\Controller\DatalistInterface;
use CommonApi\Exception\RuntimeException;
use Exception;
use stdClass;

/**
 * Datalist Controller
 *
 * @package     Molajo
 * @copyright   2014 Amy Stephen. All rights reserved.
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class Datalist implements DatalistInterface
{
    /**
     * Resource
     *
     * @var    object
     * @since  1.0
     */
    protected $resource = null;

    /**
     * Constructor
     *
     * @param  int $length
     *
     * @since  1.0
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * Get Datalist
     *
     * @param   string $list
     * @param   array  $options
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getDatalist($list, array $options = array())
    {
        $catalog_type_id = 0;
        $model           = 'Molajo//Model//Datalist//' . $list . '.xml';
        $controller      = $this->resource->get('query:///' . $model);

        if ($controller->getModelRegistry('data_object') == 'Database') {
            $results = $this->getDatalistDatabase($controller);
        } else {
            $results = $this->getDatalistValues($controller);
        }

        $multiple   = (int)$controller->getModelRegistry('multiple');
        $size       = (int)$controller->getModelRegistry('size');
        $structured = array();

        if (is_array($results) && count($results) > 0) {

            foreach ($results as $item) {

                $first          = 1;
                $row            = new stdClass();
                $row->list_name = $list;

                foreach ($item as $property => $value) {
                    if ($first === 1) {
                        $row->id = $value;
                    } else {
                        $row->value = $value;
                    }
                    $first = 0;
                }

                if ((int)$multiple === 0) {
                    $row->multiple = '';
                    $row->size     = 0;
                } else {
                    $row->multiple = ' multiple';
                    if ((int)$size === 0) {
                        $size = 5;
                    }
                }

                if ((int)$size === 0) {
                    $row->size = '';
                } else {
                    $row->size = ' size="' . (int)$size . '"';
                }

                $row->selected     = '';
                $row->no_selection = 1;
                $structured[]      = $row;
            }
        }

        return $structured;
    }

    /**
     * Get Datalist from Database
     *
     * @return  int
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getDatalistDatabase($controller)
    {
        $controller->setModelRegistry('process_events', 0);
        $controller->setModelRegistry('get_customfields', 0);
        $controller->setModelRegistry('query_object', 'list');
        $controller->setModelRegistry('use_pagination', 0);

        $catalog_type_id = $controller->getModelRegistry('criteria_catalog_type_id', null);
        if ($catalog_type_id === null) {
            $catalog_type_id = 0;
        }

        if ((string)$catalog_type_id == '*') {
            if (isset($this->runtime_data->resource->data->parameters->criteria_catalog_type_id)) {
                $catalog_type_id = $this->runtime_data->resource->data->parameters->criteria_catalog_type_id;
            }
        }

        $fields = $controller->getModelRegistry('fields');
        $count  = count($fields);
        $i      = 0;
        foreach ($fields as $field) {
            $i ++;
            $controller->select($controller->getModelRegistry('primary_prefix', 'a') . '.' . $field['name']);
            if ($i == 2 && $count == 2) {
                $controller->setDistinct(true);
                $controller->orderBy($controller->getModelRegistry('primary_prefix', 'a') . '.' . $field['name'], 'ASC');
            }
        }

        if ((int)$catalog_type_id > 0) {
            $controller->where(
                'column',
                $controller->getModelRegistry('primary_prefix', 'a') . '.' . 'catalog_type_id',
                '=',
                'integer',
                (int)$catalog_type_id
            );
        }

        try {
            return $controller->getData();

        } catch (Exception $e) {
            throw new RuntimeException ($e->getMessage());
        }
    }

    /**
     * Get Datalist from Value List
     *
     * @return  int
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getDatalistValues($controller)
    {
        return $controller->getModelRegistry('values');
    }
}
