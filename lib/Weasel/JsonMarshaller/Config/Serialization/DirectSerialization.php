<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Serialization;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonTypeName;

/**
 * Class DirectSerialization
 * @package Weasel\JsonMarshaller\Config\Serialization
 * @JsonTypeName("direct")
 */
class DirectSerialization extends PropertySerialization
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
        return "[DirectSerialization property={$this->property} include={$this->include} type={$this->realType} typeInfo={$this->typeInfo}]";
    }

}
