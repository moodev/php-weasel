<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\Deserialization;

class ClassDeserialization
{

    /**
     * @var \Weasel\XmlMarshaller\Config\Deserialization\ElementDeserialization[]
     */
    public $elements = array();

    /**
     * @var \Weasel\XmlMarshaller\Config\Deserialization\ElementWrapper[]
     */
    public $elementWrappers = array();

    /**
     * @var \Weasel\XmlMarshaller\Config\Deserialization\AttributeDeserialization[]
     */
    public $attributes = array();

    /**
     * @var \Weasel\XmlMarshaller\Config\Deserialization\ElementRefDeserialization[]
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
