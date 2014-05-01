<?php
namespace Weasel\JsonMarshaller\Config\Type;

use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;
use Weasel\JsonMarshaller\Types\JsonType;

class ScalarType extends Type {

    public function __construct($typeName = null) {
        $this->typeName = $typeName;
    }

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $typeName;

    /**
     * This is a bit of a dirty hack...
     * @var JsonType
     */
    public $jsonType;

} 