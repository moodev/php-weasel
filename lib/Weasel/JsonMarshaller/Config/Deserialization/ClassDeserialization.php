<?php
/**
 * @author Jonathan Oddy <jonathan@woaf.net>
 * @copyright 2012 Jonathan Oddy
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Deserialization;

class ClassDeserialization
{

    /**
     * @var \Weasel\JsonMarshaller\Config\Deserialization\PropertyDeserialization[]
     */
    public $properties = array();

    /**
     * @var \Weasel\JsonMarshaller\Config\Deserialization\Creator
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
     * @var \Weasel\JsonMarshaller\Config\Deserialization\TypeInfo
     */
    public $typeInfo;

}
