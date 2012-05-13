<?php
namespace PhpAnnotation;

/**
 * Created by JetBrains PhpStorm.
 * User: User
 * Date: 12/05/12
 * Time: 18:09
 * To change this template use File | Settings | File Templates.
 */
class AnnotationReader
{

    /**
     * @var \ReflectionClass
     */
    protected $class;

    protected $classAnnotations = null;
    protected $methodAnnotations = null;
    protected $propertyAnnotations = null;

    protected $propertyGetters = null;
    protected $propertySetters = null;

    protected $parser = null;
    protected $namespaces = array();

    public function __construct(\ReflectionClass $class, AnnotationConfigurator $annotations = null)
    {
        $this->class = $class;
        if (!isset($annotations)) {
            $annotations = new AnnotationConfigurator();
        }
        $this->parser = new DocblockParser($annotations);
        $this->namespaces = new PhpParser();
    }

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

    public function getClassAnnotation($annotation)
    {
        $classes = $this->getClassAnnotations();
        return isset($classes[$annotation]) ? $classes[$annotation] : null;
    }

    public function getMethodAnnotations($method)
    {
        if (isset($this->methodAnnotations)) {
            return $this->methodAnnotations;
        }

        $this->methodAnnotations[$method] = array();
        $docblock = $this->class->getMethod($method)->getDocComment();
        if ($docblock !== false) {
            $this->methodAnnotations = $this->parser->parse($docblock, "method", $this->namespaces);
        }
        return $this->methodAnnotations;

    }

    public function getMethodAnnotation($method, $annotation)
    {
        $methods = $this->getMethodAnnotations($method);
        return isset($methods[$annotation]) ? $methods[$annotation] : null;

    }

    public function getPropertyAnnotations($property)
    {
        if (isset($this->propertyAnnotations)) {
            return $this->propertyAnnotations;
        }

        $this->propertyAnnotations[$property] = array();
        $docblock = $this->class->getProperty($property)->getDocComment();
        if ($docblock !== false) {
            $this->propertyAnnotations = $this->parser->parse($docblock, "property", $this->namespaces);
        }
        return $this->propertyAnnotations;

    }

    public function getPropertyAnnotation($property, $annotation)
    {
        $properties = $this->getPropertyAnnotations($property);
        return isset($properties[$annotation]) ? $properties[$annotation] : null;
    }

    public function getGetterForProperty($property)
    {

    }

    public function getSetterForProperty($property)
    {

    }

}
