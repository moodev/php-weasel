<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */

namespace Weasel\JsonMarshaller\Config;

/**
 * Class PropertyConfigProvider
 * A really boring config provider which works off a public property containing a config object structure.
 * This might be suitable for building a configuration from some DI framework or something.
 * @package Weasel\JsonMarshaller\Config
 */
class PropertyConfigProvider implements JsonConfigProvider
{

    /**
     * @var ClassMarshaller[] Map of class names to ClassMarshallers
     */
    public $config = array();

    /**
     * @param ClassMarshaller[] $config Map of class names to ClassMarshallers
     */
    public function __construct(array $config = array())
    {
        $this->config = $config;
    }

    /**
     * Obtain the config for a named class
     * @param string $class The class to get the config for
     * @return \Weasel\JsonMarshaller\Config\ClassMarshaller The config, or null if not found
     */
    public function getConfig($class)
    {
        $class = ltrim($class, '\\');
        if (isset($this->config[$class])) {
            return $this->config[$class];
        }
        return null;
    }
}