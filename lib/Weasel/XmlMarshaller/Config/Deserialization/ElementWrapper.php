<?php
namespace PhpXmlMarshaller\Config\Deserialization;

class ElementWrapper
{


    /**
     * @var bool
     */
    public $nillable = false;


    /**
     * @var \XmlMarshaller\Config\Deserialization\ElementDeserialization
     */
    public $wraps;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $namespace;

}
