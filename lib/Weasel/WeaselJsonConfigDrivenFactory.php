<?php
namespace Weasel;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Weasel\Common\Cache\Cache;
use Weasel\Common\Cache\ArrayCache;
use Weasel\Annotation\AnnotationConfigurator;
use Weasel\JsonMarshaller\Config\JsonBootstrapConfigProvider;
use Weasel\JsonMarshaller\Config\JsonConfigProvider;
use Weasel\JsonMarshaller\Config\PropertyConfigProvider as JsonPropertyConfigProvider;
use Weasel\XmlMarshaller\Config\ConfigProvider;
use Weasel\XmlMarshaller\Config\PropertyConfigProvider as XmlPropertyConfigProvider;
use Weasel\JsonMarshaller\JsonMapper;
use Weasel\Common\Cache\CacheAwareInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Weasel\XmlMarshaller\XmlMapper;
use Doctrine\Common\Annotations\CachedReader;

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
    private $_jsonConfigFile;

    /**
     * @var string
     */
    private $_xmlConfigFile;

    /**
     * @var JsonMapper
     */
    private $_jsonMapper = null;

    /**
     * @var XmlMapper
     */
    private $_xmlMapper = null;

    public function __construct($jsonConfigFile, $xmlConfigFile = null)
    {
        $this->_jsonConfigFile = $jsonConfigFile;
        $this->_xmlConfigFile = $xmlConfigFile;
        $this->setCache(new ArrayCache());
    }

    private function _getJsonConfigMapper()
    {
        return new JsonMapper(new JsonBootstrapConfigProvider());
    }

    /**
     * @return JsonConfigProvider
     */
    private function _getJsonConfigDriver()
    {
        $mapper = $this->_getJsonConfigMapper();
        $configString = file_get_contents($this->_jsonConfigFile);
        $config = $mapper->readString($configString, '\Weasel\JsonMarshaller\Config\ClassMarshaller[string]');
        return new JsonPropertyConfigProvider($config);
    }

    /**
     * @return ConfigProvider
     */
    private function _getXmlConfigDriver()
    {
        $mapper = $this->_getJsonConfigMapper();
        $configString = file_get_contents($this->_xmlConfigFile);
        $config = $mapper->readString($configString, '\Weasel\XmlMarshaller\Config\ClassMarshaller[string]');
        return new XmlPropertyConfigProvider($config);
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
        if (!isset($this->_xmlMapper)) {
            $this->_xmlMapper = new XmlMapper($this->_getXmlConfigDriver());
        }
        return $this->_xmlMapper;
    }
}
