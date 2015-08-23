<?php
/**
 * Includes
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Resource\Configuration;

/**
 * Includes
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Includes extends Inherit
{
    /**
     * Recursively processing XML for all include statements (including those included by include)
     *
     * @return  array
     * @since   1.0.0
     */
    protected function getIncludeCode()
    {
        $pre_string  = $this->xml->asXML();

        $post_string = '';
        $done        = false;

        while ($done === false) {

            $post_string = $this->getIncludeCodeLoop($pre_string);

            if ($post_string === $pre_string) {
                $done = true;
            } else {
                $pre_string = $post_string;
            }
        }

        $this->xml = simplexml_load_string($post_string);

        if (isset($this->xml->model)) {
            $this->xml = $this->xml->model;
        }

        return $this;
    }

    /**
     * Process all include statements currently in XML
     *
     * @param   string $xml_string
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getIncludeCodeLoop($xml_string)
    {
        $pattern = '/<include (.*)="(.*)"\/>/';

        preg_match_all($pattern, $xml_string, $matches);

        $replace_this_array = $matches[0];
        $type_array         = $matches[1];
        $include_name_array = $matches[2];

        if (count($replace_this_array) === 0) {
            return $xml_string;
        }

        for ($i = 0; $i < count($replace_this_array); $i++) {

            $replace_this = $replace_this_array[$i];
            $name         = $include_name_array[$i];

            if (trim(strtolower($type_array[$i])) === 'field') {
                $model_name = $name;
                $with_this  = '<field name="' . $model_name . '"/>';

            } else {
                $model_name = 'xml://Molajo//Model//Include//' . $name . '.xml';
                $with_this  = $this->resource->get($model_name);
            }

            $xml_string = str_replace($replace_this, $with_this, $xml_string);
        }

        return $xml_string;
    }
}
