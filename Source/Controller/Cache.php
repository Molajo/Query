<?php
/**
 * Cache Controller
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

use CommonApi\Query\ModelInterface;
use CommonApi\Query\QueryBuilderInterface;

/**
 * Cache Controller
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Cache extends Event
{
    /**
     * Cache Trait
     *
     * @var     object  CommonApi\Cache\CacheTrait
     * @since   1.0.0
     */
    use \CommonApi\Cache\CacheTrait;

    /**
     * Class Constructor
     *
     * @param  ModelInterface        $model
     * @param  array                 $runtime_data
     * @param  QueryBuilderInterface $query
     * @param  callable              $schedule_event
     * @param  callable              $get_cache_callback
     * @param  callable              $set_cache_callback
     * @param  callable              $delete_cache_callback
     *
     * @since  1.0
     */
    public function __construct(
        ModelInterface $model = null,
        $runtime_data = array(),
        QueryBuilderInterface $query = null,
        $schedule_event = null,
        $get_cache_callback = null,
        $set_cache_callback = null,
        $delete_cache_callback = null
    ) {
        parent::__construct(
            $model,
            $runtime_data,
            $query,
            $schedule_event
        );

        $this->get_cache_callback    = $get_cache_callback;
        $this->set_cache_callback    = $set_cache_callback;
        $this->delete_cache_callback = $delete_cache_callback;

        $this->get_cache_callback    = null;
        $this->set_cache_callback    = null;
        $this->delete_cache_callback = null;

        $this->cache_type            = 'Cachequery';
    }

    /**
     * Get Cache Item if it is to be used for Model Registry and if it exists
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getQueryCache()
    {
        if ($this->useQueryCache() === false) {
            return $this;
        }

        $cache_item = $this->getCache(md5($this->sql));

        if ($cache_item->isHit() === true) {
            $this->query_results = $cache_item->getValue();
        }

        return $this;
    }

    /**
     * Set Cache if it is to be used for Model Registry
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setQueryCache()
    {
        if ($this->useQueryCache() === false) {
            return $this;
        }

        $this->setCache(md5($this->sql), $this->query_results);

        return $this;
    }

    /**
     * Delete Cache for a specific item or all of this type
     *
     * @param   string $key
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function deleteQueryCache($key = null)
    {
        if ($key === null) {
            return $this->clearCache();
        }

        $this->deleteCache(md5($key));

        return $this;
    }

    /**
     * Determine if Model Registry has deactivated Cache for this object
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function useQueryCache()
    {
        if ($this->getModelRegistry('cache_off', false) === true) {
            return false;
        }

        return $this->useCache();
    }
}
