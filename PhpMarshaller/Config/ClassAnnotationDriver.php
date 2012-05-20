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
     * @var ClassMarshaller
     */
    protected $config;

    public function __construct(\ReflectionClass $rClass, \PhpAnnotation\AnnotationConfigurator $configurator)
    {
        $this->annotationReader = new AnnotationReader($rClass, $configurator);
        $this->rClass = $rClass;
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


        if (isset($this->config->serialization->properties[$property])) {
            throw new \Exception("Serialization for property of name $property has already been configured.");
        }
        $getterConfig->method = $name;
        $getterConfig->type = $propertyConfig->getType();

        $this->config->serialization->properties[$property] = $getterConfig;
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

        if (isset($this->config->deserialization->properties[$property])) {
            throw new \Exception("Deserialization for property of name $property has already been configured.");
        }
        $setterConfig = new Deserialization\SetterDeserialization();
        $setterConfig->method = $name;
        $setterConfig->type = $propertyConfig->getType();

        $this->config->deserialization->properties[$property] = $setterConfig;
    }

    protected function _configureCreator(\ReflectionMethod $method) {
        $name = $method->getName();
        /**
         * @var \PhpMarshaller\Config\Annotations\JsonCreator $propertyConfig
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

    protected function _configureProperty(\ReflectionProperty $property) {
        $name = $property->getName();
        /**
         * @var \PhpMarshaller\Config\Annotations\JsonProperty $propertyConfig
         */
        $propertyConfig = $this->annotationReader->getPropertyAnnotation($name, self::_ANS . 'JsonProperty');
        if (!isset($propertyConfig)) {
            return;
        }

        $property = $propertyConfig->getName();

        if (!isset($this->config->deserialization->properties[$property])) {
            $setterConfig = new Deserialization\DirectDeserialization();
            $setterConfig->property = $name;
            $setterConfig->type = $propertyConfig->getType();

            $this->config->deserialization->properties[$property] = $setterConfig;
        }


        if (!isset($this->config->serialization->properties[$property])) {
            $getterConfig = new Serialization\DirectSerialization();
            $getterConfig->property = $name;
            $getterConfig->type = $propertyConfig->getType();

            $this->config->serialization->properties[$property] = $getterConfig;
        }

    }

    public function getConfig()
    {

        $this->config = new ClassMarshaller();
        $this->config->serialization = new Serialization\ClassSerialization();
        $this->config->deserialization = new Deserialization\ClassDeserialization();

        $methods = $this->rClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $this->_configureMethod($method);
        }

        $properties = $this->rClass->getProperties(\ReflectionProperty::IS_PUBLIC &~ \ReflectionProperty::IS_STATIC);
        foreach ($properties as $property) {
            $this->_configureProperty($property);
        }

        /**
         * @var \PhpMarshaller\Config\Annotations\JsonIgnoreProperties|null $ignorer
         */
        $ignorer = $this->annotationReader->getClassAnnotation(self::_ANS . 'JsonIgnoreProperties');
        if (isset($ignorer)) {
            // The ignorer config affects which properties we will consider.
            $this->config->deserialization->ignoreUnknown = $ignorer->getIgnoreUnknown();
            $this->config->deserialization->ignoreProperties = $ignorer->getNames();
        }

        // TODO polymorphism
        // TODO creators

        return $this->config;

    }


}
