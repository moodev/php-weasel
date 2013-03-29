<?php
namespace Weasel;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Weasel\Common\Cache\Cache;
use Weasel\Common\Cache\ArrayCache;
use Weasel\Annotation\AnnotationConfigurator;
use Weasel\JsonMarshaller\Config\AnnotationDriver as JsonAnnotationDriver;
use Weasel\JsonMarshaller\JsonMapper;
use Weasel\Common\Cache\CacheAwareInterface;
use Weasel\Annotation\DoctrineAnnotationReaderFactory;
use Doctrine\Common\Annotations\AnnotationReader;

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
     * @var AnnotationReader
     */
    public $annotationReader = null;

    public function __construct()
    {
        $this->setCache(new ArrayCache());
    }

    /**
     * @return AnnotationReader
     */
    public function getDoctrineAnnotationReaderInstance()
    {
        if (!isset($this->annotationReader)) {
            $reader = new AnnotationReader();
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
     * @return JsonMarshaller\JsonMapper
     */
    public function getJsonMapperInstance()
    {
        if (!isset($this->_jsonMapper)) {
            $driver = new JsonAnnotationDriver($this->getAnnotationReaderFactoryInstance());
            $driver->setAnnotationNamespace('\Weasel\JsonMarshaller\Config\DoctrineAnnotations');
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

    protected $_oldFactory = null;

    /**
     * @return XmlMapper
     */
    public function getXmlMapperInstance()
    {
        if (!isset($this->_oldFactory)) {
            $this->_oldFactory = new WeaselDefaultAnnotationDrivenFactory();
            $this->_autowire($this->_oldFactory);
        }
        return $this->_oldFactory->getXmlMapperInstance();
    }
}
