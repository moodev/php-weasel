<?php
namespace PhpXmlMarshaller\Config;

use PhpXmlMarshaller\Config\Annotations as Annotations;
use PhpXmlMarshaller\Config as Config;
use PhpAnnotation\AnnotationReader;

class ClassAnnotationDriver
{
    const _ANS = '\PhpXmlMarshaller\Config\Annotations\\';

    /**
     * @var \PhpAnnotation\AnnotationReader
     */
    protected $annotationReader;

    /**
     * @var \ReflectionClass
     */
    protected $rClass;

    /**
     * @var \PhpAnnotation\AnnotationConfigurator
     */
    protected $configurator;

    /**
     * @var ClassMarshaller
     */
    protected $config;

    public function __construct(\ReflectionClass $rClass, \PhpAnnotation\AnnotationConfigurator $configurator)
    {
        $this->configurator = $configurator;
        $this->annotationReader = new AnnotationReader($rClass, $configurator);
        $this->rClass = $rClass;
    }

    protected function _configureSetter(\ReflectionMethod $method) {
        $name = $method->getName();
        $defaultName = lcfirst(substr($name, 3));

        $setterConfig = new Deserialization\SetterDeserialization();
        $setterConfig->method = $name;

        /**
         * @var \PhpXmlMarshaller\Config\Annotations\XmlElement $elementConfig
         */
        $elementConfig = $this->annotationReader->getSingleMethodAnnotation($name, self::_ANS . 'XmlElement');

        if (isset($elementConfig)) {
            $this->_configureElementDeserialization($elementConfig, $setterConfig, $defaultName);
            return;
        }

        /**
         * @var \PhpXmlMarshaller\Config\Annotations\XmlAttribute $attributeConfig
         */
        $attributeConfig = $this->annotationReader->getSingleMethodAnnotation($name, self::_ANS . 'XmlAttribute');
        if (isset($elementConfig)) {
            $this->_configureAttributeDeserialization($attributeConfig, $setterConfig, $defaultName);
            return;
        }

    }

    protected function _configureElementDeserialization(Annotations\XmlElement $annot, Config\Deserialization\PropertyDeserialization $prop, $defaultName) {
        $elementConfig = new Config\Deserialization\ElementDeserialization();
        $prop->type = $annot->getType();
        $elementConfig->property = $prop;
        $elementConfig->nillable = $annot->getNillable();

        // TODO find namespace
        $namespace = $annot->getNamespace();
        $name = $annot->getName();
        if (!isset($name)) {
            $name = $defaultName;
        }

        $fullName = (!empty($namespace) ? $namespace . ":" : "") . $name;

        $this->config->deserialization->elements[$fullName] = $elementConfig;
    }

    protected function _configureAttributeDeserialization(Annotations\XmlAttribute $annot, Config\Deserialization\PropertyDeserialization $prop, $defaultName) {
        $attributeConfig = new Config\Deserialization\AttributeDeserialization();
        $prop->type = $annot->getType();
        $attributeConfig->property = $prop;

        // TODO find namespace
        $namespace = $annot->getNamespace();
        $name = $annot->getName();
        if (!isset($name)) {
            $name = $defaultName;
        }

        $fullName = (!empty($namespace) ? $namespace . ":" : "") . $name;

        $this->config->deserialization->attributes[$fullName] = $attributeConfig;
    }

    protected function _configureMethod(\ReflectionMethod $method) {
        $name = $method->getName();
        if ($method->isStatic()) {
//            $this->_configureCreator($method);
        } elseif ($method->isConstructor()) {
//            $this->_configureCreator($method);
        } elseif (strpos($name, 'get') === 0) {
//            $this->_configureGetter($method);
        } elseif (strpos($name, 'set') === 0) {
            $this->_configureSetter($method);
        }
    }

    protected function _configureProperty(\ReflectionProperty $property) {
        $name = $property->getName();

        $directConfig = new Deserialization\DirectDeserialization();
        $directConfig->property = $name;

        /**
         * @var \PhpXmlMarshaller\Config\Annotations\XmlElement $elementConfig
         */
        $elementConfig = $this->annotationReader->getSinglePropertyAnnotation($name, self::_ANS . 'XmlElement');

        if (isset($elementConfig)) {
            $this->_configureElementDeserialization($elementConfig, $directConfig, $name);
            return;
        }

        /**
         * @var \PhpXmlMarshaller\Config\Annotations\XmlAttribute $attributeConfig
         */
        $attributeConfig = $this->annotationReader->getSinglePropertyAnnotation($name, self::_ANS . 'XmlAttribute');
        if (isset($elementConfig)) {
            $this->_configureAttributeDeserialization($attributeConfig, $directConfig, $name);
            return;
        }
    }

    public function getConfig()
    {

        $this->config = new ClassMarshaller();
        $this->config->serialization = new Serialization\ClassSerialization();
        $this->config->deserialization = new Deserialization\ClassDeserialization();

        /**
         * @var Annotations\XmlDiscriminatorValue $discrimValueA
         */
        $discrimValueA = $this->annotationReader->getSingleClassAnnotation(self::_ANS . 'XmlDiscriminatorValue');
        if (isset($discrimValueA)) {
            $this->config->deserialization->discriminatorValue = $discrimValueA->getValue();
        }

        /**
         * @var Annotations\XmlDiscriminator $discrimA
         */
        $discrimA = $this->annotationReader->getSingleClassAnnotation(self::_ANS . 'XmlDiscriminator');
        if (isset($discrimA)) {
            $this->config->deserialization->discriminator = $discrimA->getValue();
        }

        $name = $this->rClass->getName();
        // TODO default namespace
        $namespace = null;

        /**
         * @var Annotations\XmlType $xmlTypeA
         */
        $xmlTypeA = $this->annotationReader->getSingleClassAnnotation(self::_ANS . 'XmlType');
        if (isset($xmlTypeA)) {
            $name = ($xmlTypeA->getName() !== null) ? $xmlTypeA->getName() : $name;
            $namespace = ($xmlTypeA->getNameSpace() !== null) ? $xmlTypeA->getNameSpace() : $namespace;

            $factoryMethod = $xmlTypeA->getFactoryMethod();
            if (isset($factoryMethod)) {
                $this->config->deserialization->factoryMethod = $factoryMethod;
                $factoryClass = $xmlTypeA->getFactoryMethod();
                if (isset($factoryClass)) {
                    $this->config->deserialization->factoryClass = $factoryClass;
                }
            }
        }

        /**
         * @var Annotations\XmlRootElement $rootA
         */
        $rootA = $this->annotationReader->getSingleClassAnnotation(self::_ANS . 'XmlRootElement');
        if (isset($rootA)) {
            $name = ($rootA->getName() !== null) ? $rootA->getName() : $name;
            $namespace = ($rootA->getNameSpace() !== null) ? $rootA->getNameSpace() : $namespace;
        }
        $this->config->deserialization->name = $name;
        $this->config->deserialization->namespace = $namespace;

        /**
         * @var Annotations\XmlSeeAlso $seeAlsoA
         */
        $seeAlsoA = $this->annotationReader->getSingleClassAnnotation(self::_ANS . 'XmlSeeAlso');
        if (isset($seeAlsoA)) {
            $this->config->deserialization->subClasses = $seeAlsoA->getValue();
        }

        $methods = $this->rClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $this->_configureMethod($method);
        }

        $properties = $this->rClass->getProperties(\ReflectionProperty::IS_PUBLIC &~ \ReflectionProperty::IS_STATIC);
        foreach ($properties as $property) {
            $this->_configureProperty($property);
        }

        return $this->config;

    }


}
