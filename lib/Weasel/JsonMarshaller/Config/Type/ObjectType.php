<?php
namespace Weasel\JsonMarshaller\Config\Type;

use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;

class ObjectType extends Type {

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $class;

    function __construct($class = null)
    {
        $this->class = $class;
    }

} 