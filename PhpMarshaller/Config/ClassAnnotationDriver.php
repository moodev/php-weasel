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
        $propertyConfig = $this->annotationReader->getSingleMethodAnnotation($name, self::_ANS . 'JsonProperty');
        if (!isset($propertyConfig)) {
            return;
        }

        $getterConfig = new Serialization\GetterSerialization();

        $property = $propertyConfig->getName();
        if (!isset($property)) {
            $property = lcfirst(substr($name, 3));
        }

        $includer = $this->annotationReader->getSingleMethodAnnotation($name, self::_ANS . 'JsonInclude');
        $getterConfig->include = $this->_getIncluderValue($includer);

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
        $propertyConfig = $this->annotationReader->getSingleMethodAnnotation($name, self::_ANS . 'JsonProperty');
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
         * @var \PhpMarshaller\Config\Annotations\JsonCreator $creatorConfig
         */
        $creatorConfig = $this->annotationReader->getSingleMethodAnnotation($name, self::_ANS . 'JsonCreator');
        if (empty($creatorConfig)) {
            return;
        }
        if (isset($this->config->deserialization->creator)) {
            throw new \Exception("Found more than one creator method! Last was $name");
        }
        $rParams = $creatorConfig->getParams();
        if (count($rParams) === 0) {
            $creator = new Deserialization\DelegateCreator();
            $creator->method = $name;
        } else {
            $creator = new Deserialization\PropertyCreator();
            $creator->method = $name;
            $i = 0;
            $paramNames = array();
            foreach ($rParams as $rParam) {
                $paramNames[] = $rParam->getName();
            }
            foreach ($creatorConfig->getParams() as $paramConfig) {
                $param = new Deserialization\Param();
                $param->name = $paramConfig->getName();
                $param->type = $paramConfig->getType();
                if (!isset($param->name)) {
                    if (!isset($paramNames[$i])) {
                        throw new \Exception("Unable to establish name of param $i of $name");
                    }
                    $param->name = $paramNames[$i];
                }
                $creator->params[] = $param;
                $i++;
            }
            if (count($creator->params) !== count($paramNames)) {
                throw new \Exception('Expected ' . count($paramNames) . ' params but found ' . count($creator->params));
            }
        }
        $this->config->deserialization->creator = $creator;
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
        $propertyConfig = $this->annotationReader->getSinglePropertyAnnotation($name, self::_ANS . 'JsonProperty');
        if (!isset($propertyConfig)) {
            return;
        }

        $propertyName = $propertyConfig->getName();
        if (!isset($propertyName)) {
            $propertyName = $name;
        }


        if (!isset($this->config->deserialization->properties[$propertyName])) {
            $setterConfig = new Deserialization\DirectDeserialization();
            $setterConfig->property = $name;
            $setterConfig->type = $propertyConfig->getType();

            $this->config->deserialization->properties[$propertyName] = $setterConfig;
        }


        if (!isset($this->config->serialization->properties[$propertyName])) {
            $getterConfig = new Serialization\DirectSerialization();
            $getterConfig->property = $name;
            $getterConfig->type = $propertyConfig->getType();

            $includer = $this->annotationReader->getSinglePropertyAnnotation($name, self::_ANS . 'JsonInclude');
            $getterConfig->include = $this->_getIncluderValue($includer);

            $this->config->serialization->properties[$propertyName] = $getterConfig;
        }

    }

    /**
     * @param Annotations\JsonInclude $includer
     * @return int
     */
    protected function _getIncluderValue($includer) {
        $val = $this->config->serialization->include;
        if (isset($includer)) {
            switch ($includer->getValue()) {
                case (Annotations\JsonInclude::$enumInclude["ALWAYS"]):
                    $val = Serialization\ClassSerialization::INCLUDE_ALWAYS;
                    break;
                case (Annotations\JsonInclude::$enumInclude["NON_DEFAULT"]):
                    $val = Serialization\ClassSerialization::INCLUDE_NON_DEFAULT;
                    break;
                case (Annotations\JsonInclude::$enumInclude["NON_EMPTY"]):
                    $val = Serialization\ClassSerialization::INCLUDE_NON_EMPTY;
                    break;
                case (Annotations\JsonInclude::$enumInclude["NON_NULL"]):
                    $val = Serialization\ClassSerialization::INCLUDE_NON_NULL;
                    break;
                default:
            }
        }
        return $val;
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
        $ignorer = $this->annotationReader->getSingleClassAnnotation(self::_ANS . 'JsonIgnoreProperties');
        if (!empty($ignorer)) {
            // The ignorer config affects which properties we will consider.
            $this->config->deserialization->ignoreUnknown = $ignorer->getIgnoreUnknown();
            $this->config->deserialization->ignoreProperties = $ignorer->getNames();
        }

        /**
         * @var \PhpMarshaller\Config\Annotations\JsonInclude $includer
         */
        $includer = $this->annotationReader->getSingleClassAnnotation(self::_ANS . 'JsonInclude');
        $this->config->serialization->include = $this->_getIncluderValue($includer);

        return $this->config;

    }


}
