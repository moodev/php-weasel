<?php
namespace PhpJsonMarshaller\Config\Deserialization;

abstract class PropertyDeserialization
{

    /**
     * @var string
     */
    public $type;

    /**
     * @var \JsonMarshaller\Config\Deserialization\TypeInfo
     */
    public $typeInfo;
}
