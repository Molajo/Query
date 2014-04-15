<?php
/**
 * Read Model Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Model;

use Exception;
use CommonApi\Model\ReadModelInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Read Model Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class ReadModel extends Model implements ReadModelInterface
{
    /**
     * Execute query and return data
     *
     * @param   string $query_object
     * @param   string $sql
     *
     * @return  mixed
     * @since   1.0
     */
    public function getData($query_object, $sql)
    {
        try {
            if ($query_object == 'result') {
                return $this->database->loadResult($sql);
            } else {
                return $this->database->loadObjectList($sql);
            }

        } catch (Exception $e) {

            throw new RuntimeException
            ('ReadModel getData Method Exception for '
            . ' Query Object: ' . $query_object
            . ' SQL: ' . $sql
            . ' ' . $e->getMessage());
        }
    }
}
