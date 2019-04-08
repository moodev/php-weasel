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
 * Class PropertyCreator
 * @package Weasel\JsonMarshaller\Config\Deserialization
 * @JsonTypeName("property")
 */
class PropertyCreator extends Creator
{
    /**
     * @var \Weasel\JsonMarshaller\Config\Deserialization\Param[]
     * @JsonProperty(type="\Weasel\JsonMarshaller\Config\Deserialization\Param[]")
     */
    public $params = array();

    public function __toString()
    {
        return "[PropertyCreator method={$this->method} params={" . implode(", ", $this->params) . "}]";
    }

}
