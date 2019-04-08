<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Deserialization;

use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonSubTypes;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonTypeInfo;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;

/**
 * Class Creator
 * @package Weasel\JsonMarshaller\Config\Deserialization
 * @JsonSubTypes({
 * @JsonSubTypes\Type("\Weasel\JsonMarshaller\Config\Deserialization\DelegateCreator"),
 * @JsonSubTypes\Type("\Weasel\JsonMarshaller\Config\Deserialization\PropertyCreator"),
 * })
 * @JsonTypeInfo(use=JsonTypeInfo::ID_NAME, include=JsonTypeInfo::AS_PROPERTY, property="creator")
 */
abstract class Creator
{

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $method;

    public function __toString()
    {
        return '[Creator method=' . $this->method . ']';
    }

}
