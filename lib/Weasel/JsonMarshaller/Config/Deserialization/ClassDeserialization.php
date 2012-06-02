<?php
namespace PhpJsonMarshaller\Config\Deserialization;

class ClassDeserialization
{

    /**
     * @var \PhpJsonMarshaller\Config\Deserialization\PropertyDeserialization[]
     */
    public $properties = array();

    /**
     * @var \JsonMarshaller\Config\Deserialization\Creator
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
     * @var \JsonMarshaller\Config\Deserialization\TypeInfo
     */
    public $typeInfo;

}
