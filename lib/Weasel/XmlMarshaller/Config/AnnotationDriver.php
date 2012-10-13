<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config;

use Weasel\XmlMarshaller\Config\Annotations as Annotations;
use Weasel\Annotation\AnnotationReader;

class AnnotationDriver implements ConfigProvider
{

    protected $classPaths = array();
    protected $configurator;

    /**
     * @var \Weasel\Common\Cache\Cache
     */
    protected $cache;

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
     * @param string $class
     * @return \Weasel\XmlMarshaller\Config\ClassMarshaller
     */
    public function getConfig($class)
    {
        $key = strtolower($class);
        if (isset($this->cache)) {
            $found = false;
            $cached = $this->cache->get($key, "XmlConfig", $found);
            if ($found) {
                return $cached;
            }
        }
        $config = $this->_getConfig($class);

        if (isset($this->cache)) {
            $this->cache->set($key, $config, "XmlConfig");
        }
        return $config;
    }

    /**
     * @param string $class
     * @return \Weasel\XmlMarshaller\Config\ClassMarshaller
     */
    protected function _getConfig($class)
    {
        $rClass = new \ReflectionClass($class);

        $classDriver = new ClassAnnotationDriver($rClass, $this->configurator);

        return $classDriver->getConfig();

    }

    public function setCache($cache)
    {
        $this->cache = $cache;
    }
}
