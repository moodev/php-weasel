<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Annotation;


use Doctrine\Common\Annotations\Reader;

class DoctrineAnnotationReaderAdapter implements IAnnotationReader
{

    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    private $annotationReader;

    /**
     * @var \ReflectionClass
     */
    private $rClass;

    public function __construct(Reader $annotationReader, \ReflectionClass $rClass)
    {
        $this->annotationReader = $annotationReader;
        $this->rClass = $rClass;
    }

    /**
     * @return array[]
     */
    public function getClassAnnotations()
    {
        return $this->annotationReader->getClassAnnotations($this->rClass);
    }

    /**
     * @param string $annotation
     * @return null|object[]
     */
    public function getClassAnnotation($annotation)
    {
        return $this->annotationReader->getClassAnnotation($this->rClass, $annotation);
    }

    /**
     * @param string $annotation
     * @return object
     */
    public function getSingleClassAnnotation($annotation)
    {
        $res = $this->annotationReader->getClassAnnotation($this->rClass, $annotation);
        return array_shift($res);
    }

    /**
     * @param string $method
     * @return array[]
     */
    public function getMethodAnnotations($method)
    {
        return $this->annotationReader->getMethodAnnotations($this->rClass->getMethod($method));
    }

    /**
     * @param string $method
     * @param string $annotation
     * @return null|object[]
     */
    public function getMethodAnnotation($method, $annotation)
    {
        return $this->annotationReader->getMethodAnnotation($this->rClass->getMethod($method), $annotation);
    }

    /**
     * @param string $method
     * @param string $annotation
     * @return null|object
     */
    public function getSingleMethodAnnotation($method, $annotation)
    {
        $res = $this->annotationReader->getMethodAnnotation($this->rClass->getMethod($method), $annotation);
        return array_shift($res);
    }

    /**
     * @param string $property
     * @return array[]
     */
    public function getPropertyAnnotations($property)
    {
        return $this->annotationReader->getPropertyAnnotations($this->rClass->getProperty($property));
    }

    /**
     * @param string $property
     * @param string $annotation
     * @return null|object[]
     */
    public function getPropertyAnnotation($property, $annotation)
    {
        return $this->annotationReader->getPropertyAnnotation($this->rClass->getProperty($property), $annotation);
    }

    /**
     * @param string $property
     * @param string $annotation
     * @return null|object
     */
    public function getSinglePropertyAnnotation($property, $annotation)
    {
        $res = $this->annotationReader->getPropertyAnnotation($this->rClass->getProperty($property), $annotation);
        return array_shift($res);
    }
}

