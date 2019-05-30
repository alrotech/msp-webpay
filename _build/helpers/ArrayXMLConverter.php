<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

/**
 * Class ArrayXMLConverter
 */
class ArrayXMLConverter
{
    /**
     * The main function for converting to an XML document.
     * Pass in a multi dimensional array and this recursively loops through and builds up an XML document.
     * @param array $data
     * @param string $rootNodeName - what you want the root node to be - defaults to data.
     * @param SimpleXMLElement $xml - should only be used recursively
     * @return string XML
     */
    public static function toXML($data, $rootNodeName = 'ResultSet', &$xml = null)
    {
        if ($xml === null) {
            $xml = simplexml_load_string('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><' . $rootNodeName . '></' . $rootNodeName . '>');
        }

        // loop through the data passed in.
        foreach ($data as $key => $value) {

            // no numeric keys in our xml please!
            if (is_numeric($key)) {
                $numeric = 1;
                $key = $rootNodeName;
            }

            // delete any char not allowed in XML element names
            $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

            // if there is another array found recursively call this function
            if (is_array($value)) {
                $node = static::isAssoc($value) || $numeric ? $xml->addChild($key) : $xml;

                // recursive call.
                if ($numeric) {
                    $key = 'anon';
                }
                static::toXml($value, $key, $node);
            } else {

                // add single node.
                $value = htmlentities($value);
                $xml->addChild($key, $value);
            }
        }

        // pass back as XML
        //return $xml->asXML();

        // if you want the XML to be formatted, use the below instead to return the XML
        $doc = new DOMDocument('1.0');
        $doc->preserveWhiteSpace = false;
        $doc->loadXML($xml->asXML());
        $doc->formatOutput = true;

        return $doc->saveXML();
    }

    /**
     * Convert an XML document to a multi dimensional array
     * Pass in an XML document (or SimpleXMLElement object) and this recursively loops through and builds a representative array
     * @param string $xml - XML document - can optionally be a SimpleXMLElement object
     * @return array ARRAY
     */
    public static function toArray($xml)
    {
        if (is_string($xml)) {
            $xml = new SimpleXMLElement($xml);
        }
        $children = $xml->children();
        if (!$children) {
            return (string) $xml;
        }
        $arr = array();
        foreach ($children as $key => $node) {

            $node = static::toArray($node);

            // support for 'anon' non-associative arrays
            if ($key === 'anon') {
                $key = count($arr);
            }

            // if the node is already set, put it into an array
            if (isset($arr[$key])) {
                if (!is_array($arr[$key]) || $arr[$key][0] === null) {
                    $arr[$key] = array($arr[$key]);
                }
                $arr[$key][] = $node;
            } else {
                $arr[$key] = $node;
            }
        }

        return $arr;
    }

    /**
     * Determine if a variable is an associative array
     * @static
     * @param mixed $array The variable to check
     * @return boolean True if is an array
     */
    public static function isAssoc($array)
    {
        return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
    }
}
