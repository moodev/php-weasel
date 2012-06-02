<?php
namespace Weasel\JsonMarshaller\Config;

use Weasel\JsonMarshaller\Config\Annotations as Annotations;
use Weasel\Annotation\AnnotationReader;

class ClassAnnotationDriver
{
    const _ANS = '\Weasel\JsonMarshaller\Config\Annotations\\';

    /**
     * @var \Weasel\Annotation\AnnotationReader
     */
    protected $annotationReader;

    /**
     * @var \ReflectionClass
     */
    protected $rClass;

    /**
     * @var \Weasel\Annotation\AnnotationConfigurator
     */
    protected $configurator;

    /**
     * @var ClassMarshaller
     */
    protected $config;

    public function __construct(\ReflectionClass $rClass, \Weasel\Annotation\AnnotationConfigurator $configurator)
    {
        $this->configurator = $configurator;
        $this->annotationReader = new AnnotationReader($rClass, $configurator);
        $this->rClass = $rClass;
    }

    protected function _configureGetter(\ReflectionMethod $method) {
        $name = $method->getName();
        /**
         * @var \Weasel\JsonMarshaller\Config\Annotations\JsonProperty $propertyConfig
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
        /**
         * @var Annotations\JsonTypeInfo $typeInfo
         * @var Annotations\JsonSubTypes $subTypes
         * @var Annotations\JsonInclude $includer
         */
        $typeInfo = $this->annotationReader->getSingleMethodAnnotation($name, self::_ANS . 'JsonTypeInfo');
        $subTypes = $this->annotationReader->getSingleMethodAnnotation($name, self::_ANS . 'JsonSubTypes');

        $getterConfig->typeInfo = $this->_getSerializationTypeInfo($typeInfo, $subTypes);
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
         * @var \Weasel\JsonMarshaller\Config\Annotations\JsonProperty $propertyConfig
         */
        $propertyConfig = $this->annotationReader->getSingleMethodAnnotation($name, self::_ANS . 'JsonProperty');
        if (!isset($propertyConfig)) {
            return;
        }

        $property = $propertyConfig->getName();
        if (!isset($property)) {
            $property = lcfirst(substr($name, 3));
        }

        /**
         * @var Annotations\JsonTypeInfo $typeInfo
         * @var Annotations\JsonSubTypes $subTypes
         */
        $typeInfo = $this->annotationReader->getSingleMethodAnnotation($name, self::_ANS . 'JsonTypeInfo');
        $subTypes = $this->annotationReader->getSingleMethodAnnotation($name, self::_ANS . 'JsonSubTypes');
        if (isset($this->config->deserialization->properties[$property])) {
            throw new \Exception("Deserialization for property of name $property has already been configured.");
        }
        $setterConfig = new Deserialization\SetterDeserialization();
        $setterConfig->method = $name;
        $setterConfig->type = $propertyConfig->getType();
        $setterConfig->typeInfo = $this->_getDeserializationTypeInfo($typeInfo, $subTypes);

        $this->config->deserialization->properties[$property] = $setterConfig;
    }

    protected function _configureCreator(\ReflectionMethod $method) {
        $name = $method->getName();
        /**
         * @var \Weasel\JsonMarshaller\Config\Annotations\JsonCreator $creatorConfig
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
         * @var \Weasel\JsonMarshaller\Config\Annotations\JsonProperty $propertyConfig
         */
        $propertyConfig = $this->annotationReader->getSinglePropertyAnnotation($name, self::_ANS . 'JsonProperty');
        if (!isset($propertyConfig)) {
            return;
        }

        $propertyName = $propertyConfig->getName();
        if (!isset($propertyName)) {
            $propertyName = $name;
        }

        /**
         * @var Annotations\JsonTypeInfo $typeInfo
         * @var Annotations\JsonSubTypes $subTypes
         */
        $typeInfo = $this->annotationReader->getSinglePropertyAnnotation($name, self::_ANS . 'JsonTypeInfo');
        $subTypes = $this->annotationReader->getSinglePropertyAnnotation($name, self::_ANS . 'JsonSubTypes');

        if (!isset($this->config->deserialization->properties[$propertyName])) {
            $setterConfig = new Deserialization\DirectDeserialization();
            $setterConfig->property = $name;
            $setterConfig->type = $propertyConfig->getType();
            $setterConfig->typeInfo = $this->_getDeserializationTypeInfo($typeInfo, $subTypes);

            $this->config->deserialization->properties[$propertyName] = $setterConfig;
        }


        if (!isset($this->config->serialization->properties[$propertyName])) {
            $getterConfig = new Serialization\DirectSerialization();
            $getterConfig->property = $name;
            $getterConfig->type = $propertyConfig->getType();

            /**
             * @var Annotations\JsonInclude $includer
             */
            $includer = $this->annotationReader->getSinglePropertyAnnotation($name, self::_ANS . 'JsonInclude');
            $getterConfig->include = $this->_getIncluderValue($includer);
            $getterConfig->typeInfo = $this->_getSerializationTypeInfo($typeInfo, $subTypes);

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

    protected function _getSubClassName(\ReflectionClass $rClass) {
        $subClassReader = new AnnotationReader($rClass, $this->configurator);
        /**
         * @var Annotations\JsonTypeName $subNameA
         */
        $subNameA = $subClassReader->getSingleClassAnnotation(self::_ANS . 'JsonTypeName');
        if (isset($subNameA)) {
            return $subNameA->getName();
        } else {
            return $rClass->getName();
        }

    }

    /**
     * @param Annotations\JsonTypeInfo $typeInfo
     * @param Annotations\JsonSubTypes $subTypes
     * @throws \Exception
     * @return Deserialization\TypeInfo|null
     */
    protected function _getSerializationTypeInfo($typeInfo, $subTypes) {

        if (!isset($typeInfo)) {
            return null;
        }

        $typeConfig = new Serialization\TypeInfo();
        switch ($typeInfo->getUse()) {
            case Annotations\JsonTypeInfo::$enumId['CLASS']:
                $typeConfig->typeInfo = Serialization\TypeInfo::TI_USE_CLASS;
                $typeConfig->typeInfoProperty = '@class';
                break;
            case Annotations\JsonTypeInfo::$enumId['CUSTOM']:
                $typeConfig->typeInfo = Serialization\TypeInfo::TI_USE_CUSTOM;
                break;
            case Annotations\JsonTypeInfo::$enumId['MINIMAL_CLASS']:
                $typeConfig->typeInfo = Serialization\TypeInfo::TI_USE_MINIMAL_CLASS;
                $typeConfig->typeInfoProperty = '@class';
                break;
            case Annotations\JsonTypeInfo::$enumId['NAME']:
                $typeConfig->typeInfo = Serialization\TypeInfo::TI_USE_NAME;
                $typeConfig->typeInfoProperty = '@name';
                break;
            case Annotations\JsonTypeInfo::$enumId['NONE']:
                $typeConfig->typeInfo = Serialization\TypeInfo::TI_USE_NONE;
                return null;
                break;

        }

        if ($typeInfo->getProperty() !== null) {
            $typeConfig->typeInfoProperty = $typeInfo->getProperty();
        }

        switch ($typeInfo->getInclude()) {
            case Annotations\JsonTypeInfo::$enumAs["PROPERTY"]:
                $typeConfig->typeInfoAs = Serialization\TypeInfo::TI_AS_PROPERTY;
                break;
            case Annotations\JsonTypeInfo::$enumAs["WRAPPER_ARRAY"]:
                $typeConfig->typeInfoAs = Serialization\TypeInfo::TI_AS_WRAPPER_ARRAY;
                break;
            case Annotations\JsonTypeInfo::$enumAs["WRAPPER_OBJECT"]:
                $typeConfig->typeInfoAs = Serialization\TypeInfo::TI_AS_WRAPPER_OBJECT;
                break;
            case Annotations\JsonTypeInfo::$enumAs["EXTERNAL_PROPERTY"]:
                $typeConfig->typeInfoAs = Serialization\TypeInfo::TI_AS_EXTERNAL_PROPERTY;
                break;
        }

        if (isset($subTypes)) {
            foreach ($subTypes->getValue() as $type) {
                switch ($typeConfig->typeInfo) {
                    case Serialization\TypeInfo::TI_USE_CLASS:
                        $subName = $type->getValue();
                        break;
                    case Serialization\TypeInfo::TI_USE_MINIMAL_CLASS:
                        $exploded = explode('\\', $type->getValue(), 2);
                        $subName = $exploded[0];
                        break;
                    case Serialization\TypeInfo::TI_USE_NAME:
                        $subName = $type->getName();
                        if (empty($subName)) {
                            $subName = $this->_getSubClassName(new \ReflectionClass($type->getValue()));
                        }
                        break;
                    case Serialization\TypeInfo::TI_USE_CUSTOM:
                    default:
                        throw new \Exception("Unsupported typeinfo mode");
                }
                $typeConfig->subTypes[$type->getValue()] = $subName;
            }
        }

        return $typeConfig;


    }
    /**
     * @param Annotations\JsonTypeInfo $typeInfo
     * @param Annotations\JsonSubTypes $subTypes
     * @throws \Exception
     * @return Deserialization\TypeInfo|null
     */
    protected function _getDeserializationTypeInfo($typeInfo, $subTypes) {

        if (!isset($typeInfo)) {
            return null;
        }

        $typeConfig = new Deserialization\TypeInfo();
        switch ($typeInfo->getUse()) {
            case Annotations\JsonTypeInfo::$enumId['CLASS']:
                $typeConfig->typeInfo = Deserialization\TypeInfo::TI_USE_CLASS;
                $typeConfig->typeInfoProperty = '@class';
                break;
            case Annotations\JsonTypeInfo::$enumId['CUSTOM']:
                $typeConfig->typeInfo = Deserialization\TypeInfo::TI_USE_CUSTOM;
                break;
            case Annotations\JsonTypeInfo::$enumId['MINIMAL_CLASS']:
                $typeConfig->typeInfo = Deserialization\TypeInfo::TI_USE_MINIMAL_CLASS;
                $typeConfig->typeInfoProperty = '@class';
                break;
            case Annotations\JsonTypeInfo::$enumId['NAME']:
                $typeConfig->typeInfo = Deserialization\TypeInfo::TI_USE_NAME;
                $typeConfig->typeInfoProperty = '@name';
                break;
            case Annotations\JsonTypeInfo::$enumId['NONE']:
                $typeConfig->typeInfo = Deserialization\TypeInfo::TI_USE_NONE;
                return null;
                break;

        }

        if ($typeInfo->getProperty() !== null) {
            $typeConfig->typeInfoProperty = $typeInfo->getProperty();
        }

        $typeConfig->typeInfoVisible = $typeInfo->getVisible();
        $typeConfig->defaultImpl = $typeInfo->getDefaultImpl();

        switch ($typeInfo->getInclude()) {
            case Annotations\JsonTypeInfo::$enumAs["PROPERTY"]:
                $typeConfig->typeInfoAs = Deserialization\TypeInfo::TI_AS_PROPERTY;
                break;
            case Annotations\JsonTypeInfo::$enumAs["WRAPPER_ARRAY"]:
                $typeConfig->typeInfoAs = Deserialization\TypeInfo::TI_AS_WRAPPER_ARRAY;
                break;
            case Annotations\JsonTypeInfo::$enumAs["WRAPPER_OBJECT"]:
                $typeConfig->typeInfoAs = Deserialization\TypeInfo::TI_AS_WRAPPER_OBJECT;
                break;
            case Annotations\JsonTypeInfo::$enumAs["EXTERNAL_PROPERTY"]:
                $typeConfig->typeInfoAs = Deserialization\TypeInfo::TI_AS_EXTERNAL_PROPERTY;
                break;
        }

        if (isset($subTypes)) {
            foreach ($subTypes->getValue() as $type) {
                switch ($typeConfig->typeInfo) {
                    case Deserialization\TypeInfo::TI_USE_CLASS:
                        $subName = $type->getValue();
                        break;
                    case Deserialization\TypeInfo::TI_USE_MINIMAL_CLASS:
                        $exploded = explode('\\', $type->getValue(), 2);
                        $subName = $exploded[0];
                        break;
                    case Deserialization\TypeInfo::TI_USE_NAME:
                        $subName = $type->getName();
                        if (empty($subName)) {
                            $subName = $this->_getSubClassName(new \ReflectionClass($type->getValue()));
                        }
                        break;
                    case Deserialization\TypeInfo::TI_USE_CUSTOM:
                    default:
                        throw new \Exception("Unsupported typeinfo mode");
                }
                $typeConfig->subTypes[$subName] = $type->getValue();
            }
        }

        return $typeConfig;


    }

    public function getConfig()
    {

        $this->config = new ClassMarshaller();
        $this->config->serialization = new Serialization\ClassSerialization();
        $this->config->deserialization = new Deserialization\ClassDeserialization();

        /**
         * @var \Weasel\JsonMarshaller\Config\Annotations\JsonInclude $includer
         */
        $includer = $this->annotationReader->getSingleClassAnnotation(self::_ANS . 'JsonInclude');
        $this->config->serialization->include = $this->_getIncluderValue($includer);

        /**
         * @var \Weasel\JsonMarshaller\Config\Annotations\JsonTypeName $typeNamer
         */
        $typeNamer = $this->annotationReader->getSingleClassAnnotation(self::_ANS . 'JsonTypeName');
        $name = null;
        if (isset($typeNamer)) {
            $name = $typeNamer->getName();
        }
        if (empty($name)) {
            // Default to the unqualified class name
            $name = $this->rClass->getName();
        }
        $this->config->deserialization->name = $name;

        /**
         * @var Annotations\JsonTypeInfo $typeInfo
         * @var Annotations\JsonSubTypes $subTypes
         */
        $typeInfo = $this->annotationReader->getSingleClassAnnotation(self::_ANS . 'JsonTypeInfo');
        $subTypes = $this->annotationReader->getSingleClassAnnotation(self::_ANS . 'JsonSubTypes');
        $this->config->deserialization->typeInfo = $this->_getDeserializationTypeInfo($typeInfo, $subTypes);
        $this->config->serialization->typeInfo = $this->_getSerializationTypeInfo($typeInfo, $subTypes);

        $methods = $this->rClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $this->_configureMethod($method);
        }

        $properties = $this->rClass->getProperties(\ReflectionProperty::IS_PUBLIC &~ \ReflectionProperty::IS_STATIC);
        foreach ($properties as $property) {
            $this->_configureProperty($property);
        }

        /**
         * @var \Weasel\JsonMarshaller\Config\Annotations\JsonIgnoreProperties|null $ignorer
         */
        $ignorer = $this->annotationReader->getSingleClassAnnotation(self::_ANS . 'JsonIgnoreProperties');
        if (!empty($ignorer)) {
            // The ignorer config affects which properties we will consider.
            $this->config->deserialization->ignoreUnknown = $ignorer->getIgnoreUnknown();
            $this->config->deserialization->ignoreProperties = $ignorer->getNames();
        }


        return $this->config;

    }


}
