<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Serialization;

use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonSubTypes;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonTypeInfo;
use Weasel\JsonMarshaller\Config\Type\Type;
use Weasel\JsonMarshaller\Config\Type\TypeParser;

/**
 * Class PropertySerialization
 * @package Weasel\JsonMarshaller\Config\Serialization
 * @JsonSubTypes({
 * @JsonSubTypes\Type("\Weasel\JsonMarshaller\Config\Serialization\DirectSerialization"),
 * @JsonSubTypes\Type("\Weasel\JsonMarshaller\Config\Serialization\GetterSerialization"),
 * })
 * @JsonTypeInfo(use=JsonTypeInfo::ID_NAME, include=JsonTypeInfo::AS_PROPERTY, property="how", visible=true)
 */
abstract class PropertySerialization
{

    /**
     * @var integer
     * @JsonProperty(type="int")
     */
    public $include;

    /**
     * @var Type
     * @JsonProperty(type="\Weasel\JsonMarshaller\Config\Type\Type")
     */
    public $realType;

    /**
     * @var \Weasel\JsonMarshaller\Config\Serialization\TypeInfo
     * @JsonProperty(type="\Weasel\JsonMarshaller\Config\Serialization\TypeInfo")
     */
    public $typeInfo;

    public $how;

    /**
     * @return string
     */
    abstract public function __toString();

    public function __set($key, $value)
    {
        if ($key == "type") {
            $this->setType($value);
        }
    }

    /**
     * @param string $value
     * @JsonProperty(type="string")
     * @deprecated exists only for backwards compat, use realType.
     */
    public function setType($value)
    {
        $this->realType = TypeParser::parseTypeString($value);
    }
}
