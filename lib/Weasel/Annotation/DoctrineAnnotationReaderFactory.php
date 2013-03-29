<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Annotation;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

class DoctrineAnnotationReaderFactory implements IAnnotationReaderFactory
{

    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    protected $annotationReader;

    /**
     * @param \Doctrine\Common\Annotations\Reader $annotationReader
     */
    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
        AnnotationRegistry::registerAutoloadNamespace('Weasel', array(__DIR__ . '/../../'));
    }

    /**
     * @param \ReflectionClass $class
     * @return \Weasel\Annotation\IAnnotationReader
     */
    public function getReaderForClass(\ReflectionClass $class)
    {
        return new DoctrineAnnotationReaderAdapter($this->annotationReader, $class);
    }

}
