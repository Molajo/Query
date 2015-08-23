<?php
/**
 * Create Model
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Model;

use CommonApi\Exception\RuntimeException;
use CommonApi\Query\CreateModelInterface;
use Exception;

/**
 * Create Model
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Create extends Log implements CreateModelInterface
{
    /**
     * Insert Data
     *
     * @param   string $sql
     *
     * @return  mixed
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function insertData($sql)
    {
        try {

            $this->database->execute($sql);

            $value = $this->database->getInsertId();

            if (is_numeric($value)) {
                return $value;
            }

            return null;

        } catch (Exception $e) {
            throw new RuntimeException(
                'Molajo\\Model\\Create::insertData Failed: '
                . ' '
                . $e->getMessage()
            );
        }
    }
}

