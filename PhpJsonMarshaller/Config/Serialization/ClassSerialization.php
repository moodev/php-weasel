<?php
namespace PhpJsonMarshaller\Config\Serialization;

class ClassSerialization
{

    const INCLUDE_ALWAYS = 1;
    const INCLUDE_NON_DEFAULT = 2;
    const INCLUDE_NON_EMPTY = 3;
    const INCLUDE_NON_NULL =4;

    /**
     * @var \PhpJsonMarshaller\Config\Serialization\PropertySerialization[]
     */
    public $properties = array();

    public $include = self::INCLUDE_ALWAYS;

    /**
     * @var \PhpJsonMarshaller\Config\Serialization\TypeInfo
     */
    public $typeInfo;
}
