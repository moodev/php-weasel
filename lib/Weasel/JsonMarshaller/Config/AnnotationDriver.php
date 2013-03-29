<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config;

use Weasel\JsonMarshaller\Config\Annotations;
use Weasel\Annotation\AnnotationConfigurator;
use Weasel\Common\Cache\CacheAwareInterface;
use Weasel\Common\Cache\Cache;
use Weasel\Annotation\AnnotationConfigProvider;

/**
 * A config provider that uses Annotations
 */
class AnnotationDriver implements JsonConfigProvider, CacheAwareInterface
{

    protected $classPaths = array();
    protected $configurator;

    /**
     * @var \Weasel\Common\Cache\Cache
     */
    protected $cache = null;

    public function __construct(AnnotationConfigProvider $annotationConfigurator = null)
    {
        $this->configurator = $annotationConfigurator;
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

        if (isset($this->cache)) {
            $this->cache->set($key, $config, "JsonConfig");
        }
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

    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function setConfigurator(AnnotationConfigurator $configurator)
    {
        $this->configurator = $configurator;
    }
}
