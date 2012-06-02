<?php
namespace PhpXmlMarshaller\Config\Serialization;

class ClassSerialization
{

    const INCLUDE_ALWAYS = 1;
    const INCLUDE_NON_DEFAULT = 2;
    const INCLUDE_NON_EMPTY = 3;
    const INCLUDE_NON_NULL =4;

    /**
     * @var \PhpXmlMarshaller\Config\Serialization\PropertySerialization[]
     */
    public $properties = array();

    public $include = self::INCLUDE_ALWAYS;

    /**
     * @var \XmlMarshaller\Config\Serialization\TypeInfo
     */
    public $typeInfo;
}
