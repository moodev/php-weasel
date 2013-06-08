<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config;

/**
 * Holder for the configuration for marshalling of a class
 */
use Weasel\JsonMarshaller\Config\Serialization\ClassSerialization;
use Weasel\JsonMarshaller\Config\Deserialization\ClassDeserialization;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;

class ClassMarshaller
{

    public function __construct()
    {
        $this->serialization = new ClassSerialization();
        $this->deserialization = new ClassDeserialization();
    }

    /**
     * @var \Weasel\JsonMarshaller\Config\Serialization\ClassSerialization
     * @JsonProperty(type="\Weasel\JsonMarshaller\Config\Serialization\ClassSerialization")
     */
    public $serialization;

    /**
     * @var \Weasel\JsonMarshaller\Config\Deserialization\ClassDeserialization
     * @JsonProperty(type="\Weasel\JsonMarshaller\Config\Serialization\ClassDeserialization")
     */
    public $deserialization;

    public function __toString()
    {
        return "[ClassMarshaller serialization=" . $this->serialization . " deserialization=" . $this->deserialization . "]";
    }

}
