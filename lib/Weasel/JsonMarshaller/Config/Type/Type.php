<?php


namespace Weasel\JsonMarshaller\Config\Type;

use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonTypeInfo;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonSubTypes;

/**
 * Class Type
 * @package Weasel\JsonMarshaller\Config\Type
 * @JsonSubTypes({
 * @JsonSubTypes\Type("\Weasel\JsonMarshaller\Config\Type\ListType"),
 * @JsonSubTypes\Type("\Weasel\JsonMarshaller\Config\Type\MapType"),
 * @JsonSubTypes\Type("\Weasel\JsonMarshaller\Config\Type\ScalarType"),
 * })
 * @JsonTypeInfo(use=JsonTypeInfo::ID_NAME, include=JsonTypeInfo::AS_PROPERTY, property="type", visible=true)
 */
abstract class Type
{

    public $type;

} 