<?php
/**
 * Registry
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Resource\Configuration;

use CommonApi\Exception\RuntimeException;
use Molajo\Resource\Api\RegistryInterface;

/**
 * Named pair storage by Namespace with local or global persistence
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Registry implements RegistryInterface
{
    /**
     * Array containing namespace registry keys
     *
     * @var    array
     * @since  1.0
     */
    protected $registryKeys = array();

    /**
     * Array containing namespace locks
     *
     * @var    array
     * @since  1.0
     */
    protected $registryLocks = array();

    /**
     * Array containing all namespace registries and associated data
     *
     * @var    array
     * @since  1.0
     */
    protected $registry = array();

    /**
     * Does it exist? Useful for verifying existence of namespace and/or namespace element.
     *  Note: Does not create the namespace or member, simply tests if it has already been created.
     *
     * Usage:
     * $this->registry->exists('Namespace', 'Optional member');
     *
     * @param   string      $namespace
     * @param   null|string $key (optional)
     *
     * @return  bool
     * @since   1.0
     */
    public function exists($namespace, $key = null)
    {
        $namespace = $this->editNamespace($namespace);

        $namespaces = $this->registryKeys;
        if (is_array($namespaces)) {
        } else {
            return false;
        }

        if (in_array($namespace, $namespaces)) {
        } else {
            return false;
        }

        if ($key === null) {
            return true;
        }

        $thisNamespace = $this->registry[$namespace];
        if (count($thisNamespace) === 0) {
            return false;
        }

        $key = strtolower($key);
        if (isset($thisNamespace[$key])) {
            return true;
        }

        return false;
    }

    /**
     * Lock registry from update.
     *
     * Usage:
     * $this->registry->lock('Namespace');
     *
     * @param   string $namespace
     *
     * @return  bool
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function lock($namespace)
    {
        $namespace = $this->editNamespace($namespace);

        if ($this->exists($namespace)) {
        } else {
            throw new RuntimeException(
                'Registry: Namespace in Lock Request does not exist.'
            );
        }

        $this->registryLocks[$namespace] = true;

        return true;
    }

    /**
     * Check to see if a registry is locked
     *
     * Usage:
     * $this->registry->checkLock('Namespace');
     *
     * @param   string $namespace
     *
     * @return  bool    true - lock is on
     *                  false - there is no lock (and possibly no registry, either)
     * @since           1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function checkLock($namespace)
    {
        $namespace = $this->editNamespace($namespace);
        if ($this->exists($namespace)) {
        } else {
            return false;
        }

        if (isset($this->registryLocks[$namespace])) {
            return true;
        }

        return false;
    }

    /**
     * Create a registry for the specified namespace
     *
     * Notes:
     * - All namespaces are set to lowercase to remove case sensitivity
     * - Throws exception if Registry Namespace is reserved
     * - Returns Namespace if already existing (use 'exists' if verification is needed prior to createRegistry)
     * - Called automatically when needed by a Set Request
     *
     * Usage:
     *  $this->registry->createRegistry('Name Space');
     *
     * @param   string $namespace
     *
     * @return  mixed|bool|array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function createRegistry($namespace)
    {
        $namespace = $this->editNamespace($namespace);

        if ($this->exists($namespace)) {

            if (isset($this->registryKeys[$namespace])) {
                throw new RuntimeException(
                    'Registry: Cannot create Namespace ' . $namespace . ' because it already exists.'
                );
            } else {
                return $this->registry[$namespace];
            }
        }

        if ($namespace === 'db' || $namespace === '*') {
            throw new RuntimeException(
                'Registry: Namespace ' . $namespace . ' is a reserved word.'
            );
        }

        if (isset($this->registryKeys[$namespace])) {
            return $this->registry[$namespace];
        }

        $this->registryKeys[] = $namespace;

        $this->registry[$namespace] = array();

        /** Returns new registry */
        return $this->registry[$namespace];
    }

    /**
     * Returns Registry Data
     *
     * Notes:
     * - Creates registry member using default if not existing and default provided
     * - Creates registry if not existing (whether or not a member was created)
     *
     * Usage:
     * $this->registry->get('Name Space', 'key value');
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
    public function get($namespace = null, $key = null, $default = null)
    {
        $namespace = $this->editNamespace($namespace);
        $key       = $this->editNamespaceKey($namespace, $key);

        if ($this->exists($namespace) === true) {
        } else {
            if ($namespace === '*') {
            } else {
                return false;
            }
        }

        if ($namespace === '*') {
            if ($key === null) {
                return $this->listRegistry(false);
            } else {
                return $this->listRegistry(true);
            }
        } elseif ($key === null) {
            return $this->getRegistry($namespace);
        } elseif ($key === '*' || strrpos($key, '*')) {
            $sort = $this->getRegistry($namespace);

            if ($key === '*') {
                $selected = $sort;
            } else {
                //@todo - combine all the wildcard logic
                if (substr($key, 0, 1) === '*') {
                    $selected  = array();
                    $searchfor = substr($key, 1, (strrpos($key, '*') - 1));
                    foreach ($sort as $key => $value) {
                        if ($key === $searchfor) {
                            $match = true;
                        } else {
                            $match = strpos($key, $searchfor);
                        }
                        if ($match) {
                            $selected[$key] = $value;
                        }
                    }
                } else {
                    $selected = array();

                    $searchfor = substr($key, 0, strrpos($key, '*'));

                    foreach ($sort as $key => $value) {
                        $match = substr($key, 0, strlen($searchfor));
                        if (strtolower($match) === strtolower($searchfor)) {
                            $selected[$key] = $value;
                        }
                    }
                }
            }

            if ($key === '*') {
                echo '<pre>';
                var_dump($selected);
                echo '</pre>';
            } else {
                return $selected;
            }

            return true;
        }

        if (in_array($namespace, $this->registryKeys)) {
            $array            = $this->registry[$namespace];
            $namespace_exists = true;
        } else {
            $array            = array();
            $namespace_exists = false;
        }

        /** Existing named pair returned */
        if (isset($array[$key])) {
            return $array[$key];
        }

        /** Not found and no create member requested */
        if ($default === null) {
            return false;
        }

        /** Create Registry and Member if needed and member default provided */
        if ($namespace_exists) {
        } else {
            $this->createRegistry($namespace);
        }

        $array[$key]                = $default;
        $this->registry[$namespace] = $array;

        return $array[$key];
    }

    /**
     * Sets the value for a specific namespace item
     *
     * Usage:
     * $this->registry->set('Name Space', 'key_name', $value);
     *
     * @param   string  $namespace
     * @param   string  $key
     * @param   mixed   $value
     * @param   boolean $match         - used as a security precaution to ensure only named parameters
     *                                 are updated via <include /> statement overrides
     *
     * @return  void|bool|Registry
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function set($namespace, $key, $value = null, $match = false)
    {
        $namespace = $this->editNamespace($namespace);

        if ($this->checkLock($namespace)) {
            throw new RuntimeException(
                'Registry: Namespace is locked. Updates are not allowed.'
            );
        }

        $key = $this->editNamespaceKey($namespace, $key);

        if ($namespace === '') {
            throw new RuntimeException(
                'Registry: Namespace is required for Set.'
            );
        }

        if ($key === '') {
            throw new RuntimeException(
                'Registry: Key is required for Set. Namespace: ' . $namespace
            );
        }

        /** Match requirement for security to ensure only named parameters are updated */
        if ($match === true) {
            $exists = $this->exists($namespace, $key);
            if ($exists === false) {
                return false;
            }
        }

        $array = $this->getRegistry($namespace);

        $array[$key] = $value;

        $this->registry[$namespace] = $array;

        return $this;
    }

    /**
     * Copy key values from one namespace registry into another, overwriting existing values
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
     * @param  string  $source_registry
     * @param   string $target_registry
     * @param   null   $filter
     *
     * @return  Registry
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function copy($source_registry, $target_registry, $filter = null)
    {
        $source_registry = $this->editNamespace($source_registry);
        $target_registry = $this->editNamespace($target_registry);

        if ($this->checkLock($target_registry)) {
            throw new RuntimeException(
                'Registry: Target Namespace: ' . $target_registry . ' is locked. May not copy into it.'
            );
        }

        if ($this->exists($source_registry)) {
        } else {
            throw new RuntimeException
            (
                'Registry: Namespace ' . $source_registry . ' requested as source of copy does not exist.'
            );
        }

        $copy = $this->getRegistry($source_registry);

        if ($filter === null || $filter === '*') {
            if (count($copy) > 0) {
                foreach ($copy as $key => $value) {
                    $this->set($target_registry, $key, $value);
                }
            }

            return $this;
        }

        if (strpos($filter, '*')) {
            $searchfor  = substr($filter, 0, strrpos($filter, '*'));
            $exactMatch = false;
        } else {
            $searchfor  = $filter;
            $exactMatch = true;
        }

        if (count($copy) > 0) {

            foreach ($copy as $key => $value) {
                $use  = false;
                $test = substr($key, 0, strlen($searchfor));
                if (strtolower($test) === strtolower($searchfor)) {
                    if ($exactMatch === true) {
                        if (strtolower($key) === strtolower($searchfor)) {
                            $use = true;
                        }
                    } else {
                        $use = true;
                    }
                }
                if ($use === true) {
                    $this->set($target_registry, $key, $value);
                }
            }
        }

        return $this;
    }

    /**
     * Merge one Namespace into another.
     *
     *  - When keys match, target value is retained
     *  - When key does not exist on the target, it is copied in
     *      In either of the above cases, when "remove_from_source" is 1, the source entry is removed
     *  - If no entries remain in the source after the merge, the registry is removed, too
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
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function merge($source_registry, $target_registry, $filter = false, $remove_from_source = 0)
    {
        $source_registry = $this->editNamespace($source_registry);
        $target_registry = $this->editNamespace($target_registry);

        if ($this->exists($source_registry)) {
        } else {
            throw new RuntimeException
            (
                'Registry: Namespace ' . $source_registry . ' requested as a source for merging does not exist.'
            );
        }

        if ($this->exists($target_registry)) {
        } else {
            throw new RuntimeException
            (
                'Registry: Namespace ' . $target_registry . ' does not exist, was requested as target of merge.'
            );
        }

        if ($remove_from_source === 1) {
            if ($this->checkLock($source_registry)) {
                throw new RuntimeException
                (
                    'Registry: Source Namespace: ' . $target_registry . ' for Merge is locked. May not remove entries.'
                );
            }
        }

        $target_registry = $this->editNamespace($target_registry);
        if ($this->checkLock($target_registry)) {
            throw new RuntimeException
            (
                'Registry: Target Namespace: ' . $target_registry . ' for Merge is locked. May not add entries.'
            );
        }

        $searchfor = '';
        if ($filter === null || trim($filter) === '' || $filter === '*') {
        } else {
            $searchfor = substr($filter, 0, strrpos($filter, '*'));
            $searchfor = strtolower(trim($searchfor));
        }

        $target = $this->getRegistry($target_registry);
        $source = $this->getRegistry($source_registry);
        foreach ($source as $key => $value) {

            $match = 0;

            if (is_null($value)) {
                //skip it.
            } elseif ($searchfor === '') {
                $match = 1;
            } elseif (trim(substr(strtolower($key), 0, strlen(strtolower($searchfor)))) === trim($searchfor)) {
                $match = 1;
            }

            if ($match === 1) {
                if (isset($target[$key])) {
                    if ($target[$key] === null) {
                        $this->set($target_registry, $key, $value);
                    }
                } else {
                    $this->set($target_registry, $key, $value);
                }
            }

            if ($remove_from_source === 1) {
                $this->delete($source_registry, $key);
            }
        }

        if (count($this->getRegistry($source_registry)) > 0) {
        } else {
            return $this->deleteRegistry($source_registry);
        }

        return true;
    }

    /**
     * Sort Namespace
     *
     * Usage:
     * $this->registry->sort('namespace');
     *
     * @param   string $namespace
     *
     * @return  Registry
     * @throws  \CommonApi\Exception\RuntimeException
     * @since   1.0
     */
    public function sort($namespace)
    {
        $namespace = $this->editNamespace($namespace);

        if ($this->exists($namespace)) {
        } else {
            throw new RuntimeException
            (
                'Registry: Cannot sort Namespace ' . $namespace . ' since it does not exist.'
            );
        }

        $sort = $this->getRegistry($namespace);
        ksort($sort);
        $this->registry[$namespace] = $sort;

        return $this;
    }

    /**
     * Deletes a registry or registry entry
     *
     * Usage:
     * $this->registry->delete('Name Space', 'key_name');
     *
     * @param   string $namespace
     * @param   string $key
     *
     * @return  Registry
     * @return  Registry
     * @throws  \CommonApi\Exception\RuntimeException
     * @since   1.0
     */
    public function delete($namespace, $key = null)
    {
        $namespace = $this->editNamespace($namespace);

        if ($this->exists($namespace)) {
        } else {
            return $this;
        }

        if ($this->checkLock($namespace)) {
            throw new RuntimeException
            (
                'Registry: Cannot delete an entry from Namespace: ' . $namespace . ' since it has been locked.'
            );
        }

        $key = strtolower($key);
        if ($key === '') {
            return $this->deleteRegistry($namespace);
        }

        $searchfor = '';
        if ($key === null || trim($key) === '' || $key === '*') {
        } else {
            $searchfor = substr($key, 0, strrpos($key, '*'));
            $searchfor = strtolower(trim($searchfor));
        }

        $copy = $this->getRegistry($namespace);
        if (count($copy) > 0) {
        } else {
            return $this; //nothing to delete
        }

        $new = array();
        foreach ($copy as $key => $value) {

            $match = 0;

            if ($searchfor === '') {
                $match = 1;
            } elseif (trim(substr(strtolower($key), 0, strlen(strtolower($searchfor)))) === trim($searchfor)) {
                $match = 1;
            }

            if ($match === 1) {
            } else {
                $new[$key] = $value;
            }
        }

        $this->deleteRegistry($namespace);

        if (count($new) > 0) {
        } else {
            return $this;
        }

        $this->createRegistry($namespace);
        $this->registry[$namespace] = $new;

        return $this;
    }

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
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function rename($namespace, $new_namespace)
    {
        $namespace = $this->editNamespace($namespace);

        if ($this->exists($namespace)) {
        } else {
            throw new RuntimeException
            (
                'Registry: Cannot rename Namespace ' . $namespace . ' since it does not exist.'
            );
        }

        if ($this->checkLock($namespace)) {
            throw new RuntimeException
            (
                'Registry: Cannot rename Namespace: ' . $namespace . ' since it has been locked.'
            );
        }

        if ($this->exists($new_namespace)) {
        } else {
            throw new RuntimeException
            (
                'Registry: Cannot rename ' . $namespace . ' to an existing registry ' . $new_namespace
            );
        }

        $existing = $this->getRegistry($namespace);
        ksort($existing);
        $this->deleteRegistry($namespace);
        $this->createRegistry($new_namespace);
        $this->registry[$new_namespace] = $existing;

        return $this;
    }

    /**
     * Returns an array containing key and name pairs for a namespace registry
     *
     * Usage:
     * $this->registry->getArray('Name Space');
     *
     * To retrieve only the key field names, not the values:
     * $this->registry->getArray('Name Space', true);
     *
     * @param string  $namespace
     * @param boolean $key_only set to true to retrieve key names
     *
     * @return array
     * @since   1.0
     */
    public function getArray($namespace, $key_only = false)
    {
        $namespace = $this->editNamespace($namespace);

        if ($this->exists($namespace)) {
        } else {
            return array();
        }

        $array = $this->getRegistry($namespace);

        if ($key_only === false) {
            return $array;
        }

        $keyArray = array();
        foreach ($array as $key => $value) {
            $keyArray[] = $key;
        }

        return $keyArray;
    }

    /**
     * Populates a registry with an array of key and name pairs
     *
     * Usage:
     * $this->registry->loadArray('Namespace', $array);
     *
     * @param string $namespace name of registry to use or create
     * @param array  $array     key and value pairs to load
     *
     * @return Registry
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function loadArray($namespace, $array = array())
    {
        if (is_array($array) && count($array) > 0) {
        } else {
            throw new RuntimeException
            (
                'Registry: Empty or missing input array provided to loadArray.'
            );
        }

        $namespace = $this->editNamespace($namespace);

        //if ($this->exists($namespace)) {
        //    throw new RuntimeException
        //    ('Registry: Namespace ' . $namespace . ' already exists. Cannot use existing namespace with loadArray.');
        // }

        $this->getRegistry($namespace);

        $this->registry[$namespace] = $array;

        return $this;
    }

    /**
     * Retrieves a list of ALL namespace registries and optionally keys/values
     *
     * Usage:
     * $this->registry->listRegistry();
     *
     * @param boolean $expand  true - returns the entire list and each registry
     *                         false - returns a list of registry names, only
     *
     * @return mixed|boolean or array
     * @since   1.0
     */
    public function listRegistry($expand = false)
    {
        if ($expand === false) {
            echo '<pre>';
            var_dump($this->registryKeys);
            echo '</pre>';

            return;
        }

        echo '<pre>';
        var_dump($this->registry);
        echo '</pre>';

        return;
    }

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
    public function getData($registry, $key = null, $query_object = null)
    {
        $registry = strtolower($registry);

        $key           = strtolower($key);
        $query_results = array();

        if ($key === null || $key === '*') {
            $results = $this->get($registry);
        } elseif ($query_object === 'result') {
            return $this->get($registry, $key);
        } else {
            $results = $this->get($registry, $key);
        }

        if (is_array($results)) {
            if (isset($results[0])) {
                if (is_object($results[0])) {
                    return $results;
                }
            }
        }

        $temp_row = new \stdClass();
        if (count($results) > 0) {
            foreach ($results as $key => $value) {
                $temp_row->$key = $value;
            }
        }
        $query_results[] = $temp_row;

        return $query_results;
    }

    /**
     * Returns the registry as an array for the specified namespace
     *
     * This is a private method used within the registry class, use get to retrieve Registry
     *
     * $this->registry->get('Name Space');
     *
     * @param   string $namespace
     *
     * @return array
     * @since   1.0
     */
    protected function getRegistry($namespace)
    {
        $namespace = $this->editNamespace($namespace);

        if ($this->exists($namespace)) {
        } else {
            $this->createRegistry($namespace);
        }

        return $this->registry[$namespace];
    }

    /**
     * Delete a Registry for specified Namespace
     *
     * @param   string $namespace
     *
     * @return  Registry
     * @since   1.0
     */
    protected function deleteRegistry($namespace)
    {
        $namespace = $this->editNamespace($namespace);

        if ($this->exists($namespace)) {
        } else {
            return $this;
        }

        $namespace = strtolower($namespace);

        $existing = $this->registryKeys;
        $keep     = array();
        $deleted  = false;
        foreach ($existing as $key => $value) {

            if ($value === $namespace) {
                $deleted = true;
            } else {
                $keep[] = $value;
            }
        }

        if ($deleted === false) {
            return $this;
        }

        sort($keep);

        $tempRegistry = $this->registry;

        $this->registry     = array();
        $this->registryKeys = array();

        foreach ($keep as $key => $value) {
            $this->registryKeys[]   = $value;
            $this->registry[$value] = $tempRegistry[$value];
        }

        return $this;
    }

    /**
     * Used internally for data validation of namespace element
     *
     * @param string $namespace
     *
     * @return string
     * @throws  \CommonApi\Exception\RuntimeException
     */
    private function editNamespace($namespace)
    {
        if ($namespace === null) {
            $namespace = '*';
        } elseif (is_string($namespace) || is_numeric($namespace)) {
            $namespace = strtolower($namespace);
            $namespace = trim($namespace);
        } else {
            throw new RuntimeException
            (
                'Registry: Namespace: is not a string.'
            );
        }

        return $namespace;
    }

    /**
     * Used internally for data validation of namespace key element
     *
     * @param string $namespace
     * @param string $key
     *
     * @return string
     * @throws  \CommonApi\Exception\RuntimeException
     */
    private function editNamespaceKey($namespace, $key = null)
    {
        if ($key === null) {
        } elseif (is_string($key) || is_numeric($key)) {
            $key = strtolower($key);
            $key = trim($key);
        } else {
            echo '<pre>';
            var_dump($key);
            echo '</pre>';
            throw new RuntimeException
            (
                'Registry: Key associated with Namespace: ' . $namespace . ' is not a string.'
            );
        }

        return $key;
    }
}
