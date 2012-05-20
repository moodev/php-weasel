<?php
namespace PhpMarshaller\Config\Deserialization;

class ClassDeserialization
{

    /**
     * @var \PhpMarshaller\Config\Deserialization\PropertyDeserialization[string]
     */
    public $properties = array();

    public $creator = null;

    /**
     * @var bool
     */
    public $ignoreUnknown;

    /**
     * @var string[]
     */
    public $ignoreProperties;

}
