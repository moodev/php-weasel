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

    public function __construct()
    {
        $this->setCache(new ArrayCache());
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
     * @return XmlMarshaller\XmlMapper
     */
    public function getXmlMapperInstance()
    {
        if (!isset($this->_xmlMapper)) {
            $driver = new XmlAnnotationDriver($this->getAnnotationReaderFactoryInstance());
            $this->_autowire($driver);
            $this->_xmlMapper = new XmlMapper($driver);
        }
        return $this->_xmlMapper;
    }

    /**
     * @return JsonMarshaller\JsonMapper
     */
    public function getJsonMapperInstance()
    {
        if (!isset($this->_jsonMapper)) {
            $driver = new JsonAnnotationDriver($this->getAnnotationReaderFactoryInstance());
            $this->_autowire($driver);
            $this->_jsonMapper = new JsonMapper($driver);
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
