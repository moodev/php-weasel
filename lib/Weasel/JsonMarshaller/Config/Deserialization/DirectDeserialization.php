<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Deserialization;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonTypeName;

/**
 * Class DirectDeserialization
 * @package Weasel\JsonMarshaller\Config\Deserialization
 * @JsonTypeName("direct")
 */
class DirectDeserialization extends PropertyDeserialization
{

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $property;

    function __construct()
    {
        $this->how = "direct";
    }

    public function __toString()
    {
        return "[DirectDeserialization property={$this->property} type={$this->realType} typeInfo={$this->typeInfo}]";
    }
}
