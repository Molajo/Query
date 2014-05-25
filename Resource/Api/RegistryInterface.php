<?php
/**
 * Registry Interface
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Resource\Api;

/**
 * Registry Interface
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
interface RegistryInterface
{
    /**
     * Verifies existence of namespace and/or namespace element.
     *
     * Usage:
     * $this->registry->exists('Namespace');
     * $this->registry->exists('Namespace', 'NamespaceElement');
     *
     * @param   string      $namespace
     * @param   null|string $key
     *
     * @return  bool
     * @since   1.0
     */
    public function exists($namespace, $key = null);

    /**
     * Lock registry from update
     *
     * Usage:
     * $this->registry->lock('Namespace');
     *
     * @param   string $namespace
     *
     * @return  bool
     * @since   1.0
     */
    public function lock($namespace);

    /**
     * Returns true if the registry is locked
     * Returns false if there is no registry and if the registry is locked
     *
     * Usage:
     * $this->registry->checkLock('Namespace');
     *
     * @param   string $namespace
     *
     * @return  bool
     * @since   1.0
     */
    public function checkLock($namespace);

    /**
     * Create a registry for the specified namespace
     *
     * Notes:
     * - All namespaces are set to lowercase to remove case sensitivity
     * - Throws exception if namespace is reserved
     * - Returns namespace if already existing (Note: Use 'exists' to verify prior to creation)
     * - Called automatically when needed by a set for a namespace or for the member of a namespace
     *
     * Usage:
     *  $this->registry->createRegistry('Namespace');
     *
     * @param   string $namespace
     *
     * @return  mixed|bool|array
     * @since   1.0
     */
    public function createRegistry($namespace);

    /**
     * Returns Registry Data
     *
     * Notes:
     * - Creates registry member using default if not existing and default provided
     * - Creates registry if not existing (whether or not a member was created)
     *
     * Usage:
     * $this->registry->get('Namespace', 'key value');
     *
     * List names of existing registry namespaces:
     * echo $this->registry->get('*');
     *
     * ... include a formatted dump of namespace contents
     * echo $this->registry->get('*', '*');
     *
     * List all entries in the specified registry namespace
     * $array = $this->registry->get('Name space');
     *
     * List only those namespace entries beginning with the wildcard value:
     * echo $this->registry->get('Name space', 'theme*');
     *
     * @param   string $namespace
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  array|bool|mixed
     * @since   1.0
     */
    public function get($namespace = null, $key = null, $default = null);

    /**
     * Sets the value for a specific namespace item
     *
     * Use $match to ensure member exists prior to setting value
     *
     * Usage:
     * $this->registry->set('Namespace', 'key_name', $value);
     *
     * @param   string  $namespace
     * @param   string  $key
     * @param   mixed   $value
     * @param   boolean $match
     *
     * @return  void|bool|RegistryInterface
     * @since   1.0
     */
    public function set($namespace, $key, $value = null, $match = false);

    /**
     * Copy key values from one namespace registry into another, overwriting existing values in the other registry
     *
     * Note:
     * If target_registry already exists, source_registry values replace existing values for matching keys
     * Key pairs on target registry remain unchanged if there are no matching pairs. Use Delete first, if desired.
     * Use merge when target registry values should remain -- not be overwritten.
     *
     * Usage:
     * $this->registry->copy('namespace-x', 'to-namespace-y');
     *
     * To copy only certain values:
     * $this->registry->copy('namespace-x', 'to-namespace-y', 'wildcard*');
     *
     * @param   string $source_registry
     * @param   string $target_registry
     * @param   null   $filter
     *
     * @return  void|$this
     * @since   1.0
     */
    public function copy($source_registry, $target_registry, $filter = null);

    /**
     * Merge one Namespace into another
     *
     *  - When keys match, target value is retained
     *  - When key does not exist on the target, it is copied in
     *      In either of the above cases, when "remove_from_source" is 1, the source entry is removed
     *  - If no entries remain in the source after the merge, the empty source registry is deleted
     *
     * Usage:
     * $this->registry->merge('namespace-x', 'to-namespace-y');
     *
     * Merge a subset of source using wildcard:
     * $this->registry->merge('namespace-x', 'to-namespace-y', 'Only These*');
     *
     * Merge a subset of source using wildcard, and then delete the source merged in:
     * $this->registry->merge('namespace-x', 'to-namespace-y', 'Only These*', 1);
     *
     * @param   string $source_registry
     * @param   string $target_registry
     * @param   bool   $filter - merge for matching keys
     * @param   int    $remove_from_source
     *
     * @return  array|bool
     * @since   1.0
     */
    public function merge($source_registry, $target_registry, $filter = false, $remove_from_source = 0);

    /**
     * Sort Namespace
     *
     * Usage:
     * $this->registry->sort('namespace');
     *
     * @param   string $namespace
     *
     * @return  $this
     * @since   1.0
     */
    public function sort($namespace);

    /**
     * Deletes a registry or registry entry
     *
     * Usage:
     * $this->registry->delete('Namespace', 'key_name');
     *
     * @param   string $namespace
     * @param   string $key
     *
     * @return  \Molajo\Resource\Configuration\Registry
     * @return  \Molajo\Resource\Configuration\Registry
     * @since   1.0
     */
    public function delete($namespace, $key = null);

    /**
     * Rename a namespace (deletes existing, creates new)
     *
     * Usage:
     * $this->registry->rename($namespace);
     *
     * @param   $namespace
     * @param   $new_namespace
     *
     * @return  $this
     * @since   1.0
     */
    public function rename($namespace, $new_namespace);

    /**
     * Returns an array containing key and name pairs for a namespace registry
     *
     * Usage:
     * $this->registry->getArray('Namespace');
     *
     * To retrieve only the key field names, not the values:
     * $this->registry->getArray('Namespace', true);
     *
     * @param string  $namespace
     * @param boolean $key_only set to true to retrieve key names
     *
     * @return array
     * @since   1.0
     */
    public function getArray($namespace, $key_only = false);

    /**
     * Populates a registry with an array of key and name pairs
     *
     * Usage:
     * $this->registry->loadArray('Namespace', $array);
     *
     * @param  string $namespace name of registry to use or create
     * @param  array  $array     key and value pairs to load
     *
     * @return  $this
     * @since   1.0
     */
    public function loadArray($namespace, $array = array());

    /**
     * Retrieves a list of ALL namespace registries and optionally keys/values
     *
     * Specify $expand = true to return the entire list, and the member names and values in each registry
     *
     * Usage:
     * $this->registry->listRegistry();
     *
     * @param   boolean $expand
     *
     * @return  mixed|boolean|array
     * @since   1.0
     */
    public function listRegistry($expand = false);

    /**
     * getData - returns Registry (comes from $model_name) as Query Results (array of objects)
     *
     * Data can be requested as a result - provide $registry, $element and true for $single result
     *
     * Use '*' in the key to retrieve all values starting with a specific phrase (ex. 'model')
     *
     * @param   string      $registry     Name of registry, for the MVC this is the $model_name
     * @param   null|string $key          Key of the named pair
     * @param   null|string $query_object Result, Item, or List
     *
     * @return  array|bool|mixed
     * @since   1.0
     */
    public function getData($registry, $key = null, $query_object = null);
}
