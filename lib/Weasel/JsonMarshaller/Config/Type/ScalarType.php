<?php

namespace Weasel\JsonMarshaller\Config\Type;

use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonTypeName;

/**
 * Class ScalarType
 * @package Weasel\JsonMarshaller\Config\Type
 * @JsonTypeName("scalar")
 */
class ScalarType extends Type
{

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $typeName;

    function __construct($typeName)
    {
        $this->type = "scalar";
        $this->typeName = $typeName;
    }

} 