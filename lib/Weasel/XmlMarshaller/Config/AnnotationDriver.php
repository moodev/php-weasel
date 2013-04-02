<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config;

use Weasel\XmlMarshaller\Config\Annotations as Annotations;
use Weasel\Annotation\AnnotationConfigurator;
use Weasel\Common\Cache\CacheAwareInterface;
use Weasel\Common\Cache\Cache;
use Weasel\Common\Annotation\IAnnotationReaderFactory;

class AnnotationDriver implements ConfigProvider, CacheAwareInterface
{

    protected $classPaths = array();

    protected $annotationNamespace = '\Weasel\XmlMarshaller\Config\Annotations';

    /**
     * @var \Weasel\Common\Annotation\IAnnotationReaderFactory
     */
    public $annotationReaderFactory;

    /**
     * @var \Weasel\Common\Cache\Cache
     */
    protected $cache;

    public function __construct(IAnnotationReaderFactory $annotationReaderFactory)
    {
        $this->annotationReaderFactory = $annotationReaderFactory;
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

        $classDriver = new ClassAnnotationDriver($rClass, $this->annotationReaderFactory, $this->annotationNamespace);

        return $classDriver->getConfig();

    }

    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param \Weasel\Common\Annotation\IAnnotationReaderFactory $annotationReaderFactory
     */
    public function setAnnotationReaderFactory(IAnnotationReaderFactory $annotationReaderFactory)
    {
        $this->annotationReaderFactory = $annotationReaderFactory;
    }

    /**
     * @param string $annotationNamespace
     */
    public function setAnnotationNamespace($annotationNamespace)
    {
        $this->annotationNamespace = $annotationNamespace;
    }
}
