<?php

namespace Weasel\JsonMarshaller\Config\Type;

use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonTypeName;

/**
 * Class ListType
 * @package Weasel\JsonMarshaller\Config\Type
 * @JsonTypeName("list")
 */
class ListType extends MapType
{

    function __construct($elementType)
    {
        $this->type = "list";
        $this->elementType = $elementType;
        $this->indexType = new ScalarType("int");
    }

} 