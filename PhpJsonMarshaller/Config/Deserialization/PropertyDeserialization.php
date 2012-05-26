<?php
namespace PhpJsonMarshaller\Config\Deserialization;

abstract class PropertyDeserialization
{

    /**
     * @var string
     */
    public $type;

    /**
     * @var \PhpJsonMarshaller\Config\Deserialization\TypeInfo
     */
    public $typeInfo;
}
