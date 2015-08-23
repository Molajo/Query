<?php
/**
 * Read Model
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Model;

use CommonApi\Exception\RuntimeException;
use CommonApi\Query\ReadModelInterface;
use Exception;

/**
 * Read Model
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Read extends Log implements ReadModelInterface
{
    /**
     * Execute query and return data
     *
     * @param   string $query_object
     * @param   string $sql
     *
     * @return  mixed
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getData($query_object, $sql)
    {
        if ($query_object === 'result') {
            $method = 'loadResult';
        } else {
            $method = 'loadObjectList';
        }

        try {
            return $this->database->$method($sql);

        } catch (Exception $e) {
            throw new RuntimeException(
                'Molajo\\Model\\ReadModel::getData Failed: '
                . ' '
                . $e->getMessage()
            );
        }
    }
}
