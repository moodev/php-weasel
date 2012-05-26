<?php
namespace PhpMarshaller\Config\Deserialization;

abstract class PropertyDeserialization
{

    /**
     * @var string
     */
    public $type;

    /**
     * @var \PhpMarshaller\Config\Deserialization\TypeInfo
     */
    public $typeInfo;
}
