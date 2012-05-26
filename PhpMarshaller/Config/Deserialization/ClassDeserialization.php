<?php
namespace PhpMarshaller\Config\Deserialization;

class ClassDeserialization
{

    /**
     * @var \PhpMarshaller\Config\Deserialization\PropertyDeserialization[]
     */
    public $properties = array();

    /**
     * @var \PhpMarshaller\Config\Deserialization\Creator
     */
    public $creator = null;

    /**
     * @var bool
     */
    public $ignoreUnknown;

    /**
     * @var string[]
     */
    public $ignoreProperties;

    /**
     * @var string
     */
    public $name;

    /**
     * @var \PhpMarshaller\Config\Deserialization\TypeInfo
     */
    public $typeInfo;

}
