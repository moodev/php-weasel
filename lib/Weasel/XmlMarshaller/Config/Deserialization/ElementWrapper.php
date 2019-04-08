<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\Deserialization;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;

class ElementWrapper
{

    /**
     * @var bool
     * @JsonProperty(type="bool")
     */
    public $nillable = false;


    /**
     * @var \Weasel\XmlMarshaller\Config\Deserialization\ElementDeserialization
     * @JsonProperty(type="\Weasel\XmlMarshaller\Config\Deserialization\ElementDeserialization")
     */
    public $wraps;

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
