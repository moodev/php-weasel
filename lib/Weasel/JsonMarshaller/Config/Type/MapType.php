<?php
namespace Weasel\JsonMarshaller\Config\Type;

use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;

class MapType extends ListType {

    /**
     * @var Type
     * @JsonProperty(type="\Weasel\JsonMarshaller\Config\Type\Type")
     */
    public $keyType;

    function __construct($keyType = null, $valueType = null)
    {
        $this->keyType = $keyType;
        parent::__construct($valueType);
    }

} 