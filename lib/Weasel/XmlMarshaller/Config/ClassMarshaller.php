<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config;

use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;

class ClassMarshaller
{

    /**
     * @var \Weasel\XmlMarshaller\Config\Serialization\ClassSerialization
     * @JsonProperty(type="\Weasel\XmlMarshaller\Config\Serialization\ClassSerialization")
     */
    public $serialization;

    /**
     * @var \Weasel\XmlMarshaller\Config\Deserialization\ClassDeserialization
     * @JsonProperty(type="\Weasel\XmlMarshaller\Config\Deserialization\ClassDeserialization")
     */
    public $deserialization;

}
