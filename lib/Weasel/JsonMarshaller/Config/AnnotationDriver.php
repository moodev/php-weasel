<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config;

use Weasel\JsonMarshaller\Config\Annotations as Annotations;
use Weasel\Annotation\AnnotationReader;

/**
 * A config provider that uses Annotations
 */
class AnnotationDriver implements JsonConfigProvider
{

    protected $classPaths = array();
    protected $configurator;
    protected $cache;

    public function __construct($logger = null)
    {
        // Create ourselves an annotation configurator of a sane type
        $this->configurator = new \Weasel\Annotation\ArrayCachingAnnotationConfigurator($logger);
    }

    /**
     * Obtain the config for a named class
     * @param string $class The class to get the config for
     * @return \Weasel\JsonMarshaller\Config\ClassMarshaller The config, or null if not found
     */
    public function getConfig($class)
    {
        $key = strtolower($class);
        if (!array_key_exists($key, $this->cache)) {
            $this->cache[$key] = $this->_getConfig($class);
        }
        return $this->cache[$key];
    }

    /**
     * _Really_ obtain the config for a named class
     * @param string $class The class to get the config for
     * @return \Weasel\JsonMarshaller\Config\ClassMarshaller The config, or null if not found
     */
    protected function _getConfig($class)
    {
        $rClass = new \ReflectionClass($class);

        // Delegate actually loading the config for the class to the ClassAnnotationDriver
        $classDriver = new ClassAnnotationDriver($rClass, $this->configurator);

        return $classDriver->getConfig();

    }

}
