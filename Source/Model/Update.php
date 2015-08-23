<?php
/**
 * Update Model
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Model;

use CommonApi\Exception\RuntimeException;
use CommonApi\Query\UpdateModelInterface;
use Exception;

/**
 * Update Model
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Update extends Create implements UpdateModelInterface
{
    /**
     * Update Data
     *
     * @param   string $sql
     *
     * @return  mixed
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function updateData($sql)
    {
        try {
            return $this->database->execute($sql);

        } catch (Exception $e) {
            throw new RuntimeException(
                'Molajo\\Model\\Update::updateData Failed: '
                . ' '
                . $e->getMessage()
            );
        }
    }
}

