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
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Weasel\Annotation\AnnotationReaderFactory;
use Weasel\Annotation\AnnotationConfigurator;
use Weasel\Common\Annotation\IAnnotationReaderFactory;

class ClassAnnotationDriver implements LoggerAwareInterface
{
    /**
     * @var \Weasel\Common\Annotation\IAnnotationReader
     */
    protected $annotationReader;

    /**
     * @var \Weasel\Annotation\AnnotationReaderFactory
     */
    protected $annotationReaderFactory;

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

    /**
     * @var string
     */
    protected $annotationNamespace = '\Weasel\XmlMarshaller\Config\Annotations\\';

    /**
     * @param \ReflectionClass $rClass A reflection for the class we're configuring
     * @param \Weasel\Common\Annotation\IAnnotationReaderFactory $annotationReaderFactory A factory for annotation readers
     * @param string $annotationNamespace namespace in which we can find the annotations.
     */
    public function __construct(\ReflectionClass $rClass, IAnnotationReaderFactory $annotationReaderFactory, $annotationNamespace = '\Weasel\XmlMarshaller\Config\Annotations')
    {
        $this->annotationReaderFactory = $annotationReaderFactory;
        $this->rClass = $rClass;
        $this->annotationNamespace = $annotationNamespace . '\\';
    }

    /**
     * @return AnnotationReader
     */
    public function getAnnotationReader()
    {
        if (!isset($this->annotationReader)) {
            $this->annotationReader = $this->annotationReaderFactory->getReaderForClass($this->rClass);
        }
        return $this->annotationReader;
    }

    protected function _configureSetter(\ReflectionMethod $method, $namespace)
    {
        $name = $method->getName();

        $setterConfig = new Deserialization\SetterDeserialization();
        $setterConfig->method = $name;
        $setterConfig->id = $method->getName();

        /**
         * @var \Weasel\XmlMarshaller\Config\IAnnotations\IXmlAttribute $attributeConfig
         */
        $attributeConfig = $this->getAnnotationReader()->getSingleMethodAnnotation($name,
            $this->annotationNamespace . 'XmlAttribute');
        if (isset($attributeConfig)) {
            $defaultName = lcfirst(substr($name, 3));
            $this->_configureAttributeDeserialization($attributeConfig, $setterConfig, $defaultName, $namespace);
            return;
        }

        $defaultName = ucfirst(substr($name, 3));

        /**
         * @var \Weasel\XmlMarshaller\Config\IAnnotations\IXmlElement $elementConfig
         */
        $elementConfig = $this->getAnnotationReader()->getSingleMethodAnnotation($name,
            $this->annotationNamespace . 'XmlElement');

        if (isset($elementConfig)) {
            $element = $this->_configureElementDeserialization($elementConfig, $setterConfig, $defaultName, $namespace);
        }

        /**
         * @var \Weasel\XmlMarshaller\Config\IAnnotations\IXmlElementRef $refConfig
         */
        $refConfig = $this->getAnnotationReader()->getSingleMethodAnnotation($name,
            $this->annotationNamespace . 'XmlElementRef');

        if (isset($refConfig)) {
            $element = $this->_configureElementRefDeserialization($refConfig, $setterConfig, $defaultName, $namespace);
        }

        /**
         * @var \Weasel\XmlMarshaller\Config\IAnnotations\IXmlElementRefs $refsConfig
         */
        $refsConfig = $this->getAnnotationReader()->getSingleMethodAnnotation($name,
            $this->annotationNamespace . 'XmlElementRefs');

        if (isset($refsConfig)) {
            $element =
                $this->_configureElementRefsDeserialization($refsConfig, $setterConfig, $defaultName, $namespace);
        }

        /**
         * @var \Weasel\XmlMarshaller\Config\IAnnotations\IXmlElementWrapper $wrapperConfig
         */
        $wrapperConfig = $this->getAnnotationReader()->getSingleMethodAnnotation($name,
            $this->annotationNamespace . 'XmlElementWrapper');
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

    protected function _configureElementRefDeserialization(IAnnotations\IXmlElementRef $annot,
                                                           Config\Deserialization\PropertyDeserialization $prop,
                                                           $defaultName, $defaultNamespace)
    {
        $elementConfig = new Config\Deserialization\ElementDeserialization();
        $prop->type = $annot->getType();
        $elementConfig->property = $prop;

        $elementConfig->ref = true;

        return $elementConfig;
    }

    protected function _configureElementRefsDeserialization(IAnnotations\IXmlElementRefs $annot,
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

    protected function _configureElementDeserialization(IAnnotations\IXmlElement $annot,
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

    protected function _configureAttributeDeserialization(IAnnotations\IXmlAttribute $annot,
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
//        } elseif ($method->isConstructor()) {
//            $this->_configureCreator($method, $namespace);
//        } elseif (substr($name, 0, 3) === 'get') {
//            $this->_configureGetter($method, $namespace);
        } elseif (substr($name, 0, 3) === 'set') {
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
         * @var \Weasel\XmlMarshaller\Config\IAnnotations\IXmlAttribute $attributeConfig
         */
        $attributeConfig = $this->getAnnotationReader()->getSinglePropertyAnnotation($name,
            $this->annotationNamespace . 'XmlAttribute');
        if (isset($attributeConfig)) {
            $defaultName = lcfirst($name);
            $this->_configureAttributeDeserialization($attributeConfig, $directConfig, $defaultName, $namespace);
            return;
        }

        $defaultName = ucfirst($name);

        /**
         * @var \Weasel\XmlMarshaller\Config\IAnnotations\IXmlElement $elementConfig
         */
        $elementConfig = $this->getAnnotationReader()->getSinglePropertyAnnotation($name,
            $this->annotationNamespace . 'XmlElement');

        if (isset($elementConfig)) {
            $element = $this->_configureElementDeserialization($elementConfig, $directConfig, $defaultName, $namespace);
        }

        /**
         * @var \Weasel\XmlMarshaller\Config\IAnnotations\IXmlElementRef $refConfig
         */
        $refConfig = $this->getAnnotationReader()->getSinglePropertyAnnotation($name,
            $this->annotationNamespace . 'XmlElementRef');

        if (isset($refConfig)) {
            $element = $this->_configureElementRefDeserialization($refConfig, $directConfig, $defaultName, $namespace);
        }

        /**
         * @var \Weasel\XmlMarshaller\Config\IAnnotations\IXmlElementRefs $refsConfig
         */
        $refsConfig = $this->getAnnotationReader()->getSinglePropertyAnnotation($name,
            $this->annotationNamespace . 'XmlElementRefs');

        if (isset($refConfig)) {
            $element =
                $this->_configureElementRefsDeserialization($refsConfig, $directConfig, $defaultName, $namespace);
        }

        /**
         * @var \Weasel\XmlMarshaller\Config\IAnnotations\IXmlElementWrapper $wrapperConfig
         */
        $wrapperConfig = $this->getAnnotationReader()->getSinglePropertyAnnotation($name,
            $this->annotationNamespace . 'XmlElementWrapper');
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
         * @var IAnnotations\IXmlDiscriminatorValue $discrimValueA
         */
        $discrimValueA = $this->getAnnotationReader()->getSingleClassAnnotation($this->annotationNamespace . 'XmlDiscriminatorValue');
        if (isset($discrimValueA)) {
            $this->config->deserialization->discriminatorValue = $discrimValueA->getValue();
        }

        /**
         * @var IAnnotations\IXmlDiscriminator $discrimA
         */
        $discrimA = $this->getAnnotationReader()->getSingleClassAnnotation($this->annotationNamespace . 'XmlDiscriminator');
        if (isset($discrimA)) {
            $this->config->deserialization->discriminator = $discrimA->getValue();
        }

        $name = $this->rClass->getName();
        $name = array_pop(explode('\\', $name));
        // TODO default namespace
        $namespace = null;

        /**
         * @var IAnnotations\IXmlType $xmlTypeA
         */
        $xmlTypeA = $this->getAnnotationReader()->getSingleClassAnnotation($this->annotationNamespace . 'XmlType');
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
         * @var IAnnotations\IXmlRootElement $rootA
         */
        $rootA = $this->getAnnotationReader()->getSingleClassAnnotation($this->annotationNamespace . 'XmlRootElement');
        if (isset($rootA)) {
            $name = ($rootA->getName() !== null) ? $rootA->getName() : $name;
            $namespace = ($rootA->getNameSpace() !== null) ? $rootA->getNameSpace() : $namespace;
        }
        $this->config->deserialization->name = $name;
        $this->config->deserialization->namespace = $namespace;

        /**
         * @var IAnnotations\IXmlSeeAlso $seeAlsoA
         */
        $seeAlsoA = $this->getAnnotationReader()->getSingleClassAnnotation($this->annotationNamespace . 'XmlSeeAlso');
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

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->annotationReaderFactory->setLogger($logger);
    }
}
