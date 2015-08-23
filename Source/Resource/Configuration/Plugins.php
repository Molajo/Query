<?php
/**
 * Plugins
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Resource\Configuration;

/**
 * Plugins
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Plugins extends AbstractAdapter
{
    /**
     * Process all plugins, filtering with valid attributes array
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setPluginsRegistry()
    {
        $this->valid_attributes = $this->data_object->get('valid_plugin_attributes');

        if ($this->processPlugins() === false) {
            return $this;
        }

        $verified_attributes = array();

        foreach ($this->xml->table->plugins->plugin as $xml_extract) {
            $attributes            = get_object_vars($xml_extract);
            $item_attributes       = ($attributes["@attributes"]);
            $plugin                = $this->verifyAttributes($item_attributes);
            $verified_attributes[] = $plugin['name'];
        }

        $this->setModelRegistryElement('plugins', $verified_attributes);

        return $this;
    }

    /**
     * Process Plugins
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processPlugins()
    {
        if (isset($this->xml->table->plugins->plugin) > 0) {
        } else {
            return false;
        }

        return true;
    }
}
