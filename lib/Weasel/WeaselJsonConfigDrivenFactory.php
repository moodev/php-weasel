<?php
namespace Weasel;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Weasel\Common\Cache\Cache;
use Weasel\Common\Cache\ArrayCache;
use Weasel\Annotation\AnnotationConfigurator;
use Weasel\JsonMarshaller\Config\AnnotationDriver as JsonAnnotationDriver;
use Weasel\JsonMarshaller\Config\JsonConfigProvider;
use Weasel\JsonMarshaller\Config\PropertyConfigProvider;
use Weasel\XmlMarshaller\Config\AnnotationDriver as XmlAnnotationDriver;
use Weasel\JsonMarshaller\JsonMapper;
use Weasel\Common\Cache\CacheAwareInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Weasel\XmlMarshaller\XmlMapper;
use Weasel\DoctrineAnnotation\DoctrineAnnotationReaderFactory;
use Doctrine\Common\Annotations\CachedReader;
use Weasel\DoctrineAnnotation\WeaselCacheAdapter;

/**
 * Class WeaselJsonConfigDrivenFactory
 * @package Weasel
 *
 * A factory which can produce mappers configured from a JSON file.
 */
class WeaselJsonConfigDrivenFactory implements LoggerAwareInterface, WeaselFactory, CacheAwareInterface
{

    /**
     * @var LoggerInterface
     */
    private $_logger;

    /**
     * @var Cache
     */
    private $_cache;

    /**
     * @var string
     */
    private $_configFile;

    /**
     * @var JsonMapper
     */
    private $_jsonMapper = null;

    public function __construct($configFile)
    {
        $this->_configFile = $configFile;
        $this->setCache(new ArrayCache());
    }

    /**
     * @return JsonConfigProvider
     */
    private function _getBootstrapConfigProvider()
    {

    }

    private function _getJsonConfigMapper()
    {
        return new JsonMapper($this->_getBootstrapConfigProvider());
    }

    /**
     * @return JsonConfigProvider
     */
    private function _getJsonConfigDriver()
    {
        $mapper = $this->_getJsonConfigMapper();
        $configString = file_get_contents($this->_configFile);
        $config = $mapper->readString($configString, '\Weasel\JsonMarshaller\ClassMarshaller[string]');
        return new PropertyConfigProvider($config);
    }

    /**
     * @return JsonMarshaller\JsonMapper
     */
    public function getJsonMapperInstance()
    {
        if (!isset($this->_jsonMapper)) {
            $this->_jsonMapper = new JsonMapper($this->_getJsonConfigDriver());
        }
        return $this->_jsonMapper;
    }

    protected function _autowire($object)
    {
        $this->_configureLogger($object);
        $this->_configureCache($object);
    }

    protected function _configureLogger($object)
    {
        if ($object instanceof LoggerAwareInterface) {
            if (isset($this->_logger)) {
                $object->setLogger($this->_logger);
            }
        }
    }

    protected function _configureCache($object)
    {
        if ($object instanceof CacheAwareInterface) {
            if (isset($this->_cache)) {
                $object->setCache($this->_cache);
            }
        }
    }

    /**
     * Sets a logger instance on the object.
     * This logger will be passed into any LoggerAware instances created by this class.
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->_logger = $logger;
    }

    /**
     * Sets a cache instance on the object.
     * This cache will be passed into any CacheAware instances created by this class.
     * @param Cache $cache
     */
    public function setCache(Cache $cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Get a fully configured XmlMapper instance.
     * @return XmlMapper
     */
    public function getXmlMapperInstance()
    {
        // TODO: Implement getXmlMapperInstance() method.
    }
}
