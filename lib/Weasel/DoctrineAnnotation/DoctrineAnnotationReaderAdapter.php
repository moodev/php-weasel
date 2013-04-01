<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\DoctrineAnnotation;


use Doctrine\Common\Annotations\Reader;
use Weasel\Common\Annotation\IAnnotationReader;

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
        $annotation = ltrim($annotation, '\\');
        $res = $this->annotationReader->getClassAnnotation($this->rClass, $annotation);
        if (!is_array($res)) {
            $res = array($res);
        }
        return $res;
    }

    /**
     * @param string $annotation
     * @return object
     */
    public function getSingleClassAnnotation($annotation)
    {
        $annotation = ltrim($annotation, '\\');
        $res = $this->annotationReader->getClassAnnotation($this->rClass, $annotation);
        if (empty($res)) {
            return null;
        }
        if (is_array($res)) {
            return array_shift($res);
        }
        return $res;
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
        $annotation = ltrim($annotation, '\\');
        $res = $this->annotationReader->getMethodAnnotation($this->rClass->getMethod($method), $annotation);
        if (!is_array($res)) {
            $res = array($res);
        }
        return $res;
    }

    /**
     * @param string $method
     * @param string $annotation
     * @return null|object
     */
    public function getSingleMethodAnnotation($method, $annotation)
    {
        $annotation = ltrim($annotation, '\\');
        $res = $this->annotationReader->getMethodAnnotation($this->rClass->getMethod($method), $annotation);
        if (empty($res)) {
            return null;
        }
        if (is_array($res)) {
            return array_shift($res);
        }
        return $res;
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
        $annotation = ltrim($annotation, '\\');
        $res = $this->annotationReader->getPropertyAnnotation($this->rClass->getProperty($property), $annotation);
        if (!is_array($res)) {
            $res = array($res);
        }
        return $res;
    }

    /**
     * @param string $property
     * @param string $annotation
     * @return null|object
     */
    public function getSinglePropertyAnnotation($property, $annotation)
    {
        $annotation = ltrim($annotation, '\\');
        $res = $this->annotationReader->getPropertyAnnotation($this->rClass->getProperty($property), $annotation);
        if (empty($res)) {
            return null;
        }
        if (is_array($res)) {
            return array_shift($res);
        }
        return $res;
    }
}

