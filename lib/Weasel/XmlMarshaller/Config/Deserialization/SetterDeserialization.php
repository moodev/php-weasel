<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\Deserialization;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonTypeName;

/**
 * Class SetterDeserialization
 * @package Weasel\XmlMarshaller\Config\Deserialization
 * @JsonTypeName("setter")
 */
class SetterDeserialization extends PropertyDeserialization
{

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $method;

}
