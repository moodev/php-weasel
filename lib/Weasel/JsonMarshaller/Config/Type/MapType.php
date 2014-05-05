<?php


namespace Weasel\JsonMarshaller\Config\Type;

use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonTypeName;

/**
 * Class MapType
 * @package Weasel\JsonMarshaller\Config\Type
 * @JsonTypeName("map")
 */
class MapType extends Type
{

    /**
     * @var Type
     * @JsonProperty(type="\Weasel\JsonMarshaller\Config\Type")
     */
    public $indexType;

    /**
     * @var Type
     * @JsonProperty(type="\Weasel\JsonMarshaller\Config\Type")
     */
    public $elementType;

    function __construct($indexType, $elementType)
    {
        $this->type = "map";
        $this->elementType = $elementType;
        $this->indexType = $indexType;
    }

} 