<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\Serialization;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonTypeName;

/**
 * Class DirectSerialization
 * @package Weasel\XmlMarshaller\Config\Serialization
 * @JsonTypeName("direct")
 */
class DirectSerialization extends PropertySerialization
{

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $property;

}
