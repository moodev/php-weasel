<?php
namespace Weasel\XmlMarshaller\Config\Deserialization;

class ElementDeserialization
{

    /**
     * @var \Weasel\XmlMarshaller\Config\Deserialization\PropertyDeserialization
     */
    public $property;

    /**
     * @var bool
     */
    public $nillable = false;

    /**
     * @var bool
     */
    public $ref = false;

    /**
     * @var string[]
     */
    public $refNameToTypeMap = null;

    public $name;

    public $namespace;

}
