<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config;

use Weasel\XmlMarshaller\Config\Annotations as Annotations;
use Weasel\XmlMarshaller\Config as Config;
use Weasel\Annotation\AnnotationReader;

class ClassAnnotationDriver
{
    const _ANS = '\Weasel\XmlMarshaller\Config\Annotations\\';

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

    protected function _configureSetter(\ReflectionMethod $method, $namespace)
    {
        $name = $method->getName();

        $setterConfig = new Deserialization\SetterDeserialization();
        $setterConfig->method = $name;
        $setterConfig->id = $method->getName();

        /**
         * @var \Weasel\XmlMarshaller\Config\Annotations\XmlAttribute $attributeConfig
         */
        $attributeConfig = $this->annotationReader->getSingleMethodAnnotation($name, self::_ANS . 'XmlAttribute');
        if (isset($attributeConfig)) {
            $defaultName = lcfirst(substr($name, 3));
            $this->_configureAttributeDeserialization($attributeConfig, $setterConfig, $defaultName, $namespace);
            return;
        }

        $defaultName = ucfirst(substr($name, 3));

        /**
         * @var \Weasel\XmlMarshaller\Config\Annotations\XmlElement $elementConfig
         */
        $elementConfig = $this->annotationReader->getSingleMethodAnnotation($name, self::_ANS . 'XmlElement');

        if (isset($elementConfig)) {
            $element = $this->_configureElementDeserialization($elementConfig, $setterConfig, $defaultName, $namespace);
        }

        /**
         * @var \Weasel\XmlMarshaller\Config\Annotations\XmlElementRef $refConfig
         */
        $refConfig = $this->annotationReader->getSingleMethodAnnotation($name, self::_ANS . 'XmlElementRef');

        if (isset($refConfig)) {
            $element = $this->_configureElementRefDeserialization($refConfig, $setterConfig, $defaultName, $namespace);
        }

        /**
         * @var \Weasel\XmlMarshaller\Config\Annotations\XmlElementRefs $refsConfig
         */
        $refsConfig = $this->annotationReader->getSingleMethodAnnotation($name, self::_ANS . 'XmlElementRefs');

        if (isset($refsConfig)) {
            $element =
                $this->_configureElementRefsDeserialization($refsConfig, $setterConfig, $defaultName, $namespace);
        }

        /**
         * @var \Weasel\XmlMarshaller\Config\Annotations\XmlElementWrapper $wrapperConfig
         */
        $wrapperConfig = $this->annotationReader->getSingleMethodAnnotation($name, self::_ANS . 'XmlElementWrapper');
        if (isset($element) && isset($wrapperConfig)) {
            $wrapper = new Config\Deserialization\ElementWrapper();
            // TODO locate real namespace
            $wrapperNamespace = $wrapperConfig->getNamespace();
            if (!isset($wrapperNamespace)) {
                $wrapperNamespace = $namespace;
            }
            $wrapperName = $wrapperConfig->getName();
            if (!isset($wrapperName)) {
                $wrapperName = $defaultName;
            }

            $wrapperFullName = (!empty($wrapperNamespace) ? $wrapperNamespace . ":" : "") . $wrapperName;

            $wrapper->name = $wrapperName;
            $wrapper->namespace = $wrapperNamespace;
            $wrapper->nillable = $wrapperConfig->getNillable();
            $wrapper->wraps = $element;

            if ($wrapperConfig->getRequired()) {
                $this->config->deserialization->requiredElements[] = $setterConfig->id;
            }
            $this->config->deserialization->elementWrappers[$wrapperFullName] = $wrapper;

        } elseif (isset($element)) {
            $this->config->deserialization->elements[] = $element;
        }

    }

    protected function _configureElementRefDeserialization(Annotations\XmlElementRef $annot,
                                                           Config\Deserialization\PropertyDeserialization $prop,
                                                           $defaultName, $defaultNamespace)
    {
        $elementConfig = new Config\Deserialization\ElementDeserialization();
        $prop->type = $annot->getType();
        $elementConfig->property = $prop;

        $elementConfig->ref = true;

        return $elementConfig;
    }

    protected function _configureElementRefsDeserialization(Annotations\XmlElementRefs $annot,
                                                            Config\Deserialization\PropertyDeserialization $prop,
                                                            $defaultName, $defaultNamespace)
    {
        $elementConfig = new Config\Deserialization\ElementDeserialization();
        $prop->type = $annot->getType();
        $elementConfig->property = $prop;

        $elementConfig->ref = true;

        $elementConfig->refNameToTypeMap = array();
        foreach ($annot->getValues() as $value) {
            // TODO locate real namespace
            $namespace = $value->getNamespace();
            if (!isset($namespace)) {
                $namespace = $defaultNamespace;
            }
            $name = $value->getName();
            if (!isset($name)) {
                $name = $defaultName;
            }
            $fullName = (!empty($namespace) ? $namespace . ":" : "") . $name;
            $elementConfig->refNameToTypeMap[$fullName] = $value->getType();
        }

        return $elementConfig;
    }

    protected function _configureElementDeserialization(Annotations\XmlElement $annot,
                                                        Config\Deserialization\PropertyDeserialization $prop,
                                                        $defaultName, $defaultNamespace)
    {
        $elementConfig = new Config\Deserialization\ElementDeserialization();
        $prop->type = $annot->getType();
        $elementConfig->property = $prop;
        $elementConfig->nillable = $annot->getNillable();

        // TODO locate real namespace
        $namespace = $annot->getNamespace();
        if (!isset($namespace)) {
            $namespace = $defaultNamespace;
        }
        $name = $annot->getName();
        if (!isset($name)) {
            $name = $defaultName;
        }

        $elementConfig->name = $name;
        $elementConfig->namespace = $namespace;

        if ($annot->getRequired()) {
            $this->config->deserialization->requiredElements[] = $prop->id;
        }

        return $elementConfig;
    }

    protected function _configureAttributeDeserialization(Annotations\XmlAttribute $annot,
                                                          Config\Deserialization\PropertyDeserialization $prop,
                                                          $defaultName, $defaultNamespace)
    {
        $attributeConfig = new Config\Deserialization\AttributeDeserialization();
        $prop->type = $annot->getType();
        $attributeConfig->property = $prop;

        // TODO locate real namespace
        $namespace = $annot->getNamespace();
        if (!isset($namespace)) {
            $namespace = $defaultNamespace;
        }
        $name = $annot->getName();
        if (!isset($name)) {
            $name = $defaultName;
        }

        $fullName = (!empty($namespace) ? $namespace . ":" : "") . $name;

        if ($annot->getRequired()) {
            $this->config->deserialization->requiredAttributes[] = $prop->id;
        }

        $this->config->deserialization->attributes[$fullName] = $attributeConfig;
    }

    protected function _configureMethod(\ReflectionMethod $method, $namespace)
    {
        $name = $method->getName();
        if ($method->isStatic()) {
//            $this->_configureCreator($method, $namespace);
        } elseif ($method->isConstructor()) {
//            $this->_configureCreator($method, $namespace);
        } elseif (strpos($name, 'get') === 0) {
//            $this->_configureGetter($method, $namespace);
        } elseif (strpos($name, 'set') === 0) {
            $this->_configureSetter($method, $namespace);
        }
    }

    protected function _configureProperty(\ReflectionProperty $property, $namespace)
    {
        $name = $property->getName();

        $directConfig = new Deserialization\DirectDeserialization();
        $directConfig->property = $name;
        $directConfig->id = '$' . $name;

        /**
         * @var \Weasel\XmlMarshaller\Config\Annotations\XmlAttribute $attributeConfig
         */
        $attributeConfig = $this->annotationReader->getSinglePropertyAnnotation($name, self::_ANS . 'XmlAttribute');
        if (isset($attributeConfig)) {
            $defaultName = lcfirst($name);
            $this->_configureAttributeDeserialization($attributeConfig, $directConfig, $defaultName, $namespace);
            return;
        }

        $defaultName = ucfirst($name);

        /**
         * @var \Weasel\XmlMarshaller\Config\Annotations\XmlElement $elementConfig
         */
        $elementConfig = $this->annotationReader->getSinglePropertyAnnotation($name, self::_ANS . 'XmlElement');

        if (isset($elementConfig)) {
            $element = $this->_configureElementDeserialization($elementConfig, $directConfig, $defaultName, $namespace);
        }

        /**
         * @var \Weasel\XmlMarshaller\Config\Annotations\XmlElementRef $refConfig
         */
        $refConfig = $this->annotationReader->getSinglePropertyAnnotation($name, self::_ANS . 'XmlElementRef');

        if (isset($refConfig)) {
            $element = $this->_configureElementRefDeserialization($refConfig, $directConfig, $defaultName, $namespace);
        }

        /**
         * @var \Weasel\XmlMarshaller\Config\Annotations\XmlElementRefs $refsConfig
         */
        $refsConfig = $this->annotationReader->getSinglePropertyAnnotation($name, self::_ANS . 'XmlElementRefs');

        if (isset($refConfig)) {
            $element =
                $this->_configureElementRefsDeserialization($refsConfig, $directConfig, $defaultName, $namespace);
        }

        /**
         * @var \Weasel\XmlMarshaller\Config\Annotations\XmlElementWrapper $wrapperConfig
         */
        $wrapperConfig = $this->annotationReader->getSinglePropertyAnnotation($name, self::_ANS . 'XmlElementWrapper');
        if (isset($element) && isset($wrapperConfig)) {
            $wrapper = new Config\Deserialization\ElementWrapper();
            // TODO locate real namespace
            $wrapperNamespace = $wrapperConfig->getNamespace();
            if (!isset($wrapperNamespace)) {
                $wrapperNamespace = $namespace;
            }
            $wrapperName = $wrapperConfig->getName();
            if (!isset($wrapperName)) {
                $wrapperName = $defaultName;
            }

            $wrapperFullName = (!empty($wrapperNamespace) ? $wrapperNamespace . ":" : "") . $wrapperName;

            $wrapper->name = $wrapperName;
            $wrapper->namespace = $wrapperNamespace;
            $wrapper->nillable = $wrapperConfig->getNillable();
            $wrapper->wraps = $element;

            if ($wrapperConfig->getRequired()) {
                $this->config->deserialization->requiredElements[] = $directConfig->id;
            }
            $this->config->deserialization->elementWrappers[$wrapperFullName] = $wrapper;

        } elseif (isset($element)) {
            $this->config->deserialization->elements[] = $element;
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
        $name = array_pop(explode('\\', $name));
        // TODO default namespace
        $namespace = null;

        /**
         * @var Annotations\XmlType $xmlTypeA
         */
        $xmlTypeA = $this->annotationReader->getSingleClassAnnotation(self::_ANS . 'XmlType');
        if (isset($xmlTypeA)) {
            // TODO work out wtf name and namespace mean.

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
            $this->_configureMethod($method, $namespace);
        }

        $properties = $this->rClass->getProperties(\ReflectionProperty::IS_PUBLIC & ~\ReflectionProperty::IS_STATIC);
        foreach ($properties as $property) {
            $this->_configureProperty($property, $namespace);
        }

        return $this->config;

    }


}
