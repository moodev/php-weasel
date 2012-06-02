<?php
namespace PhpXmlMarshaller\Config\Deserialization;

class ClassDeserialization
{

    /**
     * @var \PhpXmlMarshaller\Config\Deserialization\ElementDeserialization[]
     */
    public $elements = array();

    /**
     * @var \PhpXmlMarshaller\Config\Deserialization\ElementWrapper[]
     */
    public $elementWrappers = array();

    /**
     * @var \PhpXmlMarshaller\Config\Deserialization\AttributeDeserialization[]
     */
    public $attributes = array();

    /**
     * @var \PhpXmlMarshaller\Config\Deserialization\ElementRefDeserialization[]
     */
    public $elementRefs = array();

    /**
     * @var string[]
     */
    public $requiredElements = array();

    /**
     * @var string[]
     */
    public $requiredAttributes = array();

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $namespace;

    /**
     * @var string[]
     */
    public $subClasses;

    /**
     * @var string
     */
    public $discriminator;

    /**
     * @var string
     */
    public $discriminatorValue;

    /**
     * @var string
     */
    public $factoryClass;

    /**
     * @var string
     */
    public $factoryMethod;

}
