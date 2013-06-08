<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\Deserialization;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;

class ClassDeserialization
{

    /**
     * @var \Weasel\XmlMarshaller\Config\Deserialization\ElementDeserialization[]
     * @JsonProperty(type="\Weasel\XmlMarshaller\Config\Deserialization\ElementDeserialization[string]")
     */
    public $elements = array();

    /**
     * @var \Weasel\XmlMarshaller\Config\Deserialization\ElementWrapper[]
     * @JsonProperty(type="\Weasel\XmlMarshaller\Config\Deserialization\ElementWrapper[string]")
     */
    public $elementWrappers = array();

    /**
     * @var \Weasel\XmlMarshaller\Config\Deserialization\AttributeDeserialization[]
     * @JsonProperty(type="\Weasel\XmlMarshaller\Config\Deserialization\AttributeDeserialization[string]")
     */
    public $attributes = array();

    /**
     * @var string[]
     * @JsonProperty(type="string[]")
     */
    public $requiredElements = array();

    /**
     * @var string[]
     * @JsonProperty(type="string[]")
     */
    public $requiredAttributes = array();

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $name;

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $namespace;

    /**
     * @var string[]
     * @JsonProperty(type="string[]")
     */
    public $subClasses;

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $discriminator;

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $discriminatorValue;

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $factoryClass;

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $factoryMethod;

}
