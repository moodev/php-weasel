<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Annotation;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Weasel\Common\Annotation\IAnnotationReaderFactory;

/**
 * Class AnnotationReaderFactory
 * @package Weasel\Annotation
 * @deprecated Use the DoctrineAnnotation driven reader.
 */
class AnnotationReaderFactory implements IAnnotationReaderFactory, LoggerAwareInterface
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var AnnotationConfigProvider
     */
    protected $configProvider;

    /**
     * @param AnnotationConfigProvider $configProvider
     */
    public function __construct(AnnotationConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * @param \ReflectionClass $class
     * @return \Weasel\Common\Annotation\IAnnotationReader
     */
    public function getReaderForClass(\ReflectionClass $class)
    {
        $reader = new AnnotationReader($class, $this->configProvider);
        if (isset($this->logger)) {
            $reader->setLogger($this->logger);
        }
        return $reader;
    }


    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
