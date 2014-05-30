<?php
/**
 * Mock Database
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Database;

use CommonApi\Controller\ControllerInterface;
use CommonApi\Database\DatabaseInterface;

/**
 * Class MockDatabase
 *
 * Generates a registry for testing when used with the web and ModelRegistryQuery
 *
 * @package Molajo\Controller
 */
class MockDatabase implements DatabaseInterface
{
    public function escape($value)
    {

    }

    public function loadResult($sql)
    {

    }

    public function loadObjectList($sql)
    {

    }

    public function execute($sql)
    {

    }

    public function getInsertId()
    {

    }
}
