<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Deserialization;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonSubTypes;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonTypeInfo;
use Weasel\JsonMarshaller\Config\Type\Type;
use Weasel\JsonMarshaller\Config\Type\TypeParser;

/**
 * Class PropertyDeserialization
 * @package Weasel\JsonMarshaller\Config\Deserialization
 * @JsonSubTypes({
 * @JsonSubTypes\Type("\Weasel\JsonMarshaller\Config\Deserialization\Param"),
 * @JsonSubTypes\Type("\Weasel\JsonMarshaller\Config\Deserialization\SetterDeserialization"),
 * @JsonSubTypes\Type("\Weasel\JsonMarshaller\Config\Deserialization\DirectDeserialization"),
 * })
 * @JsonTypeInfo(use=JsonTypeInfo::ID_NAME, include=JsonTypeInfo::AS_PROPERTY, property="how", visible=true)
 */
abstract class PropertyDeserialization
{

    /**
     * @var Type
     * @JsonProperty(type="\Weasel\JsonMarshaller\Config\Type\Type")
     */
    public $realType;

    /**
     * @var \Weasel\JsonMarshaller\Config\Deserialization\TypeInfo
     * @JsonProperty(type="\Weasel\JsonMarshaller\Config\Deserialization\TypeInfo")
     */
    public $typeInfo;

    /**
     * @var bool Should type checking be strict?
     * @JsonProperty(type="bool")
     */
    public $strict = true;

    public $how;

    /**
     * @param string $value
     * @JsonProperty(type="string")
     * @deprecated exists only for backwards compat, use realType.
     */
    public function setType($value)
    {
        $this->realType = TypeParser::parseTypeString($value);
    }

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

}
