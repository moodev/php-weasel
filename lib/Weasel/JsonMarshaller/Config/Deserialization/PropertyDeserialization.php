<?php
namespace Weasel\JsonMarshaller\Config\Deserialization;

abstract class PropertyDeserialization
{

    /**
     * @var string
     */
    public $type;

    /**
     * @var \Weasel\JsonMarshaller\Config\Deserialization\TypeInfo
     */
    public $typeInfo;
}
