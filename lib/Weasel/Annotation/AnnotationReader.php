<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Annotation;

class AnnotationReader
{

    /**
     * @var \ReflectionClass
     */
    protected $class;

    /**
     * @var null|array[]
     */
    protected $classAnnotations = null;
    /**
     * @var null|array[]
     */
    protected $methodAnnotations = null;
    /**
     * @var null|array[]
     */
    protected $propertyAnnotations = null;

    protected $parser = null;
    protected $namespaces = array();

    protected $otherNamespaces = array();

    protected $logger;

    /**
     * @var PhpParser
     */
    protected $nsParser;

    /**
     * @param \ReflectionClass $class
     * @param AnnotationConfigProvider $annotations
     */
    public function __construct(\ReflectionClass $class, AnnotationConfigProvider $annotations)
    {
        $this->class = $class;
        $this->parser = new DocblockParser($annotations);
        $this->logger = $annotations->getLogger();
        $this->nsParser = new PhpParser($this->logger);
        $this->namespaces = $this->nsParser->parseClass($class);
    }

    /**
     * @return array[]
     */
    public function getClassAnnotations()
    {
        if (isset($this->classAnnotations)) {
            return $this->classAnnotations;
        }

        $docblock = $this->class->getDocComment();
        if ($docblock === false) {
            $this->classAnnotations = array();
        } else {
            $this->classAnnotations = $this->parser->parse($docblock, "class", $this->namespaces);
        }
        return $this->classAnnotations;
    }

    /**
     * @param string $annotation
     * @return null|object[]
     */
    public function getClassAnnotation($annotation)
    {
        $classes = $this->getClassAnnotations();
        return isset($classes[$annotation]) ? $classes[$annotation] : null;
    }

    /**
     * @param object[] $annotations
     * @return object|null
     * @throws \Exception
     */
    protected function _singleAnnotation($annotations)
    {
        if (empty($annotations)) {
            return null;
        }
        if (count($annotations) > 1) {
            throw new \Exception("Attempt to get single annotation when there are multiple");
        }
        return array_shift($annotations);
    }

    /**
     * @param string $annotation
     * @return object
     */
    public function getSingleClassAnnotation($annotation)
    {
        return $this->_singleAnnotation($this->getClassAnnotation($annotation));
    }

    /**
     * @param \Reflection|\ReflectionMethod|\ReflectionProperty $rThing
     * @return array
     */
    protected function _getDeclaredNamespaces($rThing)
    {
        /**
         * @var \ReflectionClass $dClass
         */
        $dClass = $rThing->getDeclaringClass();

        if ($dClass != $this->class) {
            $fullName = $dClass->getNamespaceName() . '\\' . $dClass->getName();
            if (!isset($this->otherNamespaces[$fullName])) {
                $this->otherNamespaces[$fullName] = $this->nsParser->parseClass($dClass);
            }
            return $this->otherNamespaces[$fullName];
        }
        return $this->namespaces;
    }

    /**
     * @param string $method
     * @return array[]
     */
    public function getMethodAnnotations($method)
    {
        if (isset($this->methodAnnotations[$method])) {
            return $this->methodAnnotations[$method];
        }

        $this->methodAnnotations[$method] = array();
        $rMethod = $this->class->getMethod($method);
        $docblock = $rMethod->getDocComment();
        if ($docblock !== false) {
            $this->methodAnnotations[$method] = $this->parser->parse($docblock, "method",
                                                                     $this->_getDeclaredNamespaces($rMethod)
            );
        }
        return $this->methodAnnotations[$method];

    }

    /**
     * @param string $method
     * @param string $annotation
     * @return null|object[]
     */
    public function getMethodAnnotation($method, $annotation)
    {
        $methods = $this->getMethodAnnotations($method);
        return (isset($methods[$annotation]) ? $methods[$annotation] : null);

    }

    /**
     * @param string $method
     * @param string $annotation
     * @return null|object
     */
    public function getSingleMethodAnnotation($method, $annotation)
    {
        return $this->_singleAnnotation($this->getMethodAnnotation($method, $annotation));
    }

    /**
     * @param string $property
     * @return array[]
     */
    public function getPropertyAnnotations($property)
    {
        if (isset($this->propertyAnnotations[$property])) {
            return $this->propertyAnnotations[$property];
        }

        $this->propertyAnnotations[$property] = array();
        $rProperty = $this->class->getProperty($property);
        $docblock = $rProperty->getDocComment();
        if ($docblock !== false) {
            $this->propertyAnnotations[$property] = $this->parser->parse($docblock, "property",
                                                                         $this->_getDeclaredNamespaces($rProperty)
            );
        }
        return $this->propertyAnnotations[$property];

    }

    /**
     * @param string $property
     * @param string $annotation
     * @return null|object[]
     */
    public function getPropertyAnnotation($property, $annotation)
    {
        $properties = $this->getPropertyAnnotations($property);
        return isset($properties[$annotation]) ? $properties[$annotation] : null;
    }

    /**
     * @param string $property
     * @param string $annotation
     * @return null|object
     */
    public function getSinglePropertyAnnotation($property, $annotation)
    {
        return $this->_singleAnnotation($this->getPropertyAnnotation($property, $annotation));
    }

}
