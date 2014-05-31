<?php
/**
 * Model Registry Trait
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query;

use CommonApi\Query\ModelRegistryInterface;

/**
 * Model Registry Trait
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class Registry
{
    /**
     * Model Registry
     *
     * @var     object  Molajo\Query\ModelRegistryTrait
     * @since   1.0
     */
    use ModelRegistryTrait;

    /**
     * Constructor
     *
     * @param  ModelRegistryInterface $mr
     *
     * @since  1.0
     */
    public function __construct(
        ModelRegistryInterface $mr
    ) {
        $this->mr = $mr;
    }
}
