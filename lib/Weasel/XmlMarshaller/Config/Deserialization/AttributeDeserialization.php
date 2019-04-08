<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\Deserialization;

use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;

class AttributeDeserialization
{

    /**
     * @var \Weasel\XmlMarshaller\Config\Deserialization\PropertyDeserialization
     * @JsonProperty(type="\Weasel\XmlMarshaller\Config\Deserialization\PropertyDeserialization")
     */
    public $property;

}
