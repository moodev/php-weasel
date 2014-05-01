<?php
namespace Weasel\JsonMarshaller\Config\Type;

use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;

class ListType extends Type {

    /**
     * @var Type
     * @JsonProperty(type="\Weasel\JsonMarshaller\Config\Type\Type")
     */
    public $valueType;

    function __construct($valueType = null)
    {
        $this->valueType = $valueType;
    }

} 