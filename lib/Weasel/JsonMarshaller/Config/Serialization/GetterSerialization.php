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
 * Class GetterSerialization
 * @package Weasel\JsonMarshaller\Config\Serialization
 * @JsonTypeName("getter")
 */
class GetterSerialization extends PropertySerialization
{

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $method;

    function __construct($method = null, $type = null, $include = null, $typeInfo = null)
    {
        $this->method = $method;
        $this->include = $include;
        $this->typeInfo = $typeInfo;
        $this->how = "getter";
        $this->setType($type);
    }

    public function __toString()
    {
        return "[GetterSerialization method={$this->method} include={$this->include} type={$this->realType} typeInfo={$this->typeInfo}]";
    }


}
