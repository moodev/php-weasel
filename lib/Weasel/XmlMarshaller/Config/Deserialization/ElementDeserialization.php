<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\Deserialization;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;

class ElementDeserialization
{

    /**
     * @var \Weasel\XmlMarshaller\Config\Deserialization\PropertyDeserialization
     * @JsonProperty(type="\Weasel\XmlMarshaller\Config\Deserialization\PropertyDeserialization")
     */
    public $property;

    /**
     * @var bool
     * @JsonProperty(type="bool")
     */
    public $nillable = false;

    /**
     * @var bool
     * @JsonProperty(type="bool")
     */
    public $ref = false;

    /**
     * @var string[]
     * @JsonProperty(type="string[string]")
     */
    public $refNameToTypeMap = null;

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

}
