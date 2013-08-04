<?php
namespace Weasel;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Weasel\Common\Cache\Cache;
use Weasel\Common\Cache\ArrayCache;
use Weasel\Annotation\AnnotationConfigurator;
use Weasel\XmlMarshaller\Config\AnnotationDriver as XmlAnnotationDriver;
use Weasel\JsonMarshaller\Config\AnnotationDriver as JsonAnnotationDriver;
use Weasel\JsonMarshaller\JsonMapper;
use Weasel\XmlMarshaller\XmlMapper;
use Weasel\Common\Cache\CacheAwareInterface;
use Weasel\Annotation\AnnotationReaderFactory;

/**
 * Class WeaselDefaultAnnotationDrivenFactory
 * @package Weasel
 * @deprecated Doctrine driven annotations are the future. Use WeaselDoctrineAnnotationDrivenFactory.
 *
 * A factory which can produce JsonMappers and XmlMappers configured using the Weasel\Annotation library.
 * This is considered deprecated in favour of WeaselDoctrineAnnotationDrivenFactory.
 */
class WeaselDefaultAnnotationDrivenFactory implements LoggerAwareInterface, WeaselFactory, CacheAwareInterface
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
     * @var AnnotationConfigurator
     */
    private $_configurator = null;

    /**
     * @var JsonMapper
     */
    private $_jsonMapper = null;

    /**
     * @var AnnotationReaderFactory
     */
    private $_annotationReaderFactory = null;

    /**
     * @var XmlMapper
     */
    private $_xmlMapper = null;

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
     * @return AnnotationConfigurator
     */
    public function getAnnotationConfigProviderInstance()
    {
        if (!isset($this->_configurator)) {
            $configurator = new AnnotationConfigurator();
            $this->_autowire($configurator);
            $this->_configurator = $configurator;
        }
        return $this->_configurator;
    }

    /**
     * @return Annotation\AnnotationReaderFactory
     */
    public function getAnnotationReaderFactoryInstance()
    {
        if (!isset($this->_annotationReaderFactory)) {
            $factory = new AnnotationReaderFactory($this->getAnnotationConfigProviderInstance());
            $this->_autowire($factory);
            $this->_annotationReaderFactory = $factory;
        }
        return $this->_annotationReaderFactory;
    }

    /**
     * @return XmlAnnotationDriver
     */
    public function getXmlDriverInstance()
    {
        if (!isset($this->_xmlDriver)) {
            $driver = new XmlAnnotationDriver($this->getAnnotationReaderFactoryInstance());
            $this->_autowire($driver);
            $this->_xmlDriver = $driver;
        }
        return $this->_xmlDriver;
    }

    /**
     * @return XmlMarshaller\XmlMapper
     */
    public function getXmlMapperInstance()
    {
        if (!isset($this->_xmlMapper)) {
            $this->_xmlMapper = new XmlMapper($this->getXmlDriverInstance());
        }
        return $this->_xmlMapper;
    }

    /**
     * @return JsonAnnotationDriver
     */
    public function getJsonDriverInstance()
    {
        if (!isset($this->_jsonDriver)) {
            $driver = new JsonAnnotationDriver($this->getAnnotationReaderFactoryInstance());
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
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->_logger = $logger;
    }

    public function setCache(Cache $cache)
    {
        $this->_cache = $cache;
    }
}
