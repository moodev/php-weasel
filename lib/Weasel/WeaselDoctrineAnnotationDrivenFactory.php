<?php
namespace Weasel;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Weasel\Common\Cache\Cache;
use Weasel\Common\Cache\ArrayCache;
use Weasel\Annotation\AnnotationConfigurator;
use Weasel\JsonMarshaller\Config\AnnotationDriver as JsonAnnotationDriver;
use Weasel\XmlMarshaller\Config\AnnotationDriver as XmlAnnotationDriver;
use Weasel\JsonMarshaller\JsonMapper;
use Weasel\Common\Cache\CacheAwareInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Weasel\XmlMarshaller\XmlMapper;
use Weasel\DoctrineAnnotation\DoctrineAnnotationReaderFactory;
use Doctrine\Common\Annotations\CachedReader;
use Weasel\DoctrineAnnotation\WeaselCacheAdapter;

/**
 * Class WeaselDoctrineAnnotationDrivenFactory
 * @package Weasel
 *
 * A factory which can produce JsonMappers and XmlMappers configured using the Doctrine\Common\Annotation library.
 */
class WeaselDoctrineAnnotationDrivenFactory implements LoggerAwareInterface, WeaselFactory, CacheAwareInterface
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
     * @var JsonMapper
     */
    private $_jsonMapper = null;

    /**
     * @var DoctrineAnnotationReaderFactory
     */
    private $_annotationReaderFactory = null;

    /**
     * @var XmlMapper
     */
    private $_xmlMapper = null;

    /**
     * @var AnnotationReader
     */
    public $annotationReader = null;

    private $_jsonDriver = null;
    private $_xmlDriver = null;


    /**
     * @var bool Should the type checkers be strict?
     */
    private $_strict = true;

    public function __construct($strict = true)
    {
        $this->setCache(new ArrayCache());
        $this->_strict = $strict;
    }

    /**
     * @return AnnotationReader
     */
    public function getDoctrineAnnotationReaderInstance()
    {
        if (!isset($this->annotationReader)) {
            $reader = new AnnotationReader();
            if (isset($this->_cache)) {
                $reader = new CachedReader($reader, new WeaselCacheAdapter($this->_cache));
            }
            $this->_autowire($reader);
            $this->annotationReader = $reader;
        }
        return $this->annotationReader;
    }

    /**
     * @return Annotation\AnnotationReaderFactory
     */
    public function getAnnotationReaderFactoryInstance()
    {
        if (!isset($this->_annotationReaderFactory)) {
            $factory = new DoctrineAnnotationReaderFactory($this->getDoctrineAnnotationReaderInstance());
            $this->_autowire($factory);
            $this->_annotationReaderFactory = $factory;
        }
        return $this->_annotationReaderFactory;
    }

    /**
     * @return JsonAnnotationDriver
     */
    public function getJsonDriverInstance()
    {
        if (!isset($this->_jsonDriver)) {
            $driver = new JsonAnnotationDriver($this->getAnnotationReaderFactoryInstance());
            $driver->setAnnotationNamespace('\Weasel\JsonMarshaller\Config\DoctrineAnnotations');
            $this->_autowire($driver);
            $this->_jsonDriver = $driver;
        }
        return $this->_jsonDriver;
    }

    /**
     * @return JsonMarshaller\JsonMapper
     */
    public function getJsonMapperInstance()
    {
        if (!isset($this->_jsonMapper)) {
            $this->_jsonMapper = new JsonMapper($this->getJsonDriverInstance(), $this->_strict);
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
     * @return XmlAnnotationDriver
     */
    public function getXmlDriverInstance()
    {
        if (!isset($this->_xmlDriver)) {
            $driver = new XmlAnnotationDriver($this->getAnnotationReaderFactoryInstance());
            $driver->setAnnotationNamespace('\Weasel\XmlMarshaller\Config\DoctrineAnnotations');
            $this->_autowire($driver);
            $this->_xmlDriver = $driver;
        }
        return $this->_xmlDriver;
    }

    /**
     * @return XmlMapper
     */
    public function getXmlMapperInstance()
    {
        if (!isset($this->_xmlMapper)) {
            $this->_xmlMapper = new XmlMapper($this->getXmlDriverInstance());
        }
        return $this->_xmlMapper;
    }
}
