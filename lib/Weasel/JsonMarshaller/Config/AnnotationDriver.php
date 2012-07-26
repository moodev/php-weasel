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

    /**
     * @var \Weasel\Common\Cache\Cache
     */
    protected $cache = null;

    public function __construct($logger = null, $annotationConfigurator = null, $cache = null)
    {
        if (isset($annotationConfigurator)) {
            $this->configurator = $annotationConfigurator;
        } else {
            // Create ourselves an annotation configurator of a sane type
            $this->configurator = new \Weasel\Annotation\AnnotationConfigurator($logger);
        }
        $this->setCache($cache);
    }

    /**
     * Obtain the config for a named class
     * @param string $class The class to get the config for
     * @return \Weasel\JsonMarshaller\Config\ClassMarshaller The config, or null if not found
     */
    public function getConfig($class)
    {
        $key = strtolower($class);
        if (isset($this->cache)) {
            $found = false;
            $cached = $this->cache->get($key, "JsonConfig", $found);
            if ($found) {
                return $cached;
            }
        }
        $config = $this->_getConfig($class);

        $this->cache->set($key, $config, "JsonConfig");
        return $config;
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

    public function setCache($cache)
    {
        $this->cache = $cache;
    }
}
