<?php
/**
 * Delete Model
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Model;

use CommonApi\Exception\RuntimeException;
use CommonApi\Query\DeleteModelInterface;
use Exception;

/**
 * Delete Model
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Delete extends Update implements DeleteModelInterface
{
    /**
     * Delete Data
     *
     * @param   string $sql
     *
     * @return  mixed
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function deleteData($sql)
    {
        try {
            return $this->database->execute($sql);

        } catch (Exception $e) {
            throw new RuntimeException(
                'Molajo\\Model\\Delete::deleteData Failed: '
                . ' '
                . $e->getMessage()
            );
        }
    }
}

