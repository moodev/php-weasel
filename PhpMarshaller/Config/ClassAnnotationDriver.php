<?php
namespace PhpMarshaller\Config;

use PhpMarshaller\Config\Annotations as Annotations;
use PhpAnnotation\AnnotationReader;

class ClassAnnotationDriver
{
    const _ANS = '\PhpMarshaller\Config\Annotations\\';

    /**
     * @var \PhpAnnotation\AnnotationReader
     */
    protected $annotationReader;

    /**
     * @var \ReflectionClass
     */
    protected $rClass;

    /**
     * @var Serialization\ClassSerialization
     */
    protected $serializationConfig;

    /**
     * @var Deserialization\ClassDeserialization
     */
    protected $deserializationConfig;

    public function __construct(\ReflectionClass $rClass, \PhpAnnotation\AnnotationConfigurator $configurator)
    {
        $this->annotationReader = new AnnotationReader($rClass, $configurator);
    }

    protected function _configureGetter(\ReflectionMethod $method) {
        $name = $method->getName();
        /**
         * @var \PhpMarshaller\Config\Annotations\JsonProperty $propertyConfig
         */
        $propertyConfig = $this->annotationReader->getMethodAnnotation($name, self::_ANS . 'JsonProperty');
        if (!isset($propertyConfig)) {
            return;
        }

        $getterConfig = new Serialization\GetterSerialization();

        $property = $propertyConfig->getName();
        if (!isset($property)) {
            $property = lcfirst(substr($name, 3));
        }


        if (isset($this->serializationConfig->properties[$property])) {
            throw new \Exception("Serialization for property of name $property has already been configured.");
        }
        $getterConfig->method = $name;
        $getterConfig->type = $propertyConfig->getType();

        /**
         * @var \PhpMarshaller\Config\Annotations\JsonSerialize $jsonSerialize
         */
        $jsonSerialize = $this->annotationReader->getMethodAnnotation($name, self::_ANS . 'JsonSerialize');
        if (isset($jsonSerialize)) {
            // Type to serialize as has been overridden
            $getterConfig->type = $jsonSerialize->getAs();
        }

        $this->serializationConfig->properties[$property] = $getterConfig;
    }

    protected function _configureSetter(\ReflectionMethod $method) {
        $name = $method->getName();
        /**
         * @var \PhpMarshaller\Config\Annotations\JsonProperty $propertyConfig
         */
        $propertyConfig = $this->annotationReader->getMethodAnnotation($name, self::_ANS . 'JsonProperty');
        if (!isset($propertyConfig)) {
            return;
        }

        $property = $propertyConfig->getName();
        if (!isset($property)) {
            $property = lcfirst(substr($name, 3));
        }

        if (isset($this->deserializationConfig->properties[$property])) {
            throw new \Exception("Deserialization for property of name $property has already been configured.");
        }
        $setterConfig = new Deserialization\SetterDeserialization();
        $setterConfig->method = $name;
        $setterConfig->type = $propertyConfig->getType();

        $this->deserializationConfig->properties[$property] = $setterConfig;
    }

    protected function _configureCreator(\ReflectionMethod $method) {
        $name = $method->getName();
        /**
         * @var \PhpMarshaller\Config\Annotations\JsonProperty $propertyConfig
         */
        $creatorConfig = $this->annotationReader->getMethodAnnotation($name, self::_ANS . 'JsonCreator');
        if (!isset($creatorConfig)) {
            return;
        }

    }

    protected function _configureMethod(\ReflectionMethod $method) {
        $name = $method->getName();
        if ($method->isStatic()) {
            $this->_configureCreator($method);
        } elseif ($method->isConstructor()) {
            $this->_configureCreator($method);
        } elseif (strpos($name, 'get') === 0) {
            $this->_configureGetter($method);
        } elseif (strpos($name, 'set') === 0) {
            $this->_configureSetter($method);
        }
    }

    public function getConfig()
    {

        $this->serializationConfig = new Serialization\ClassSerialization();
        $this->deserializationConfig = new Deserialization\ClassDeserialization();
        $ignoreUnknown = false;

        $methods = $this->rClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $this->_configureMethod($method);
        }


        $properties = $this->rClass->getProperties(\ReflectionProperty::IS_PUBLIC);

        /**
         * @var \PhpMarshaller\Config\Annotations\JsonIgnoreProperties|null $ignorer
         */
        $ignorer = $this->annotationReader->getClassAnnotation(self::_ANS . 'JsonIgnoreProperties');
        if (isset($ignorer)) {
            // The ignorer config affects which properties we will consider.
            $ignoreUnknown = $ignorer->getIgnoreUnknown();
            $properties = array_diff($properties, $ignorer->getNames());
        }
    }


}
