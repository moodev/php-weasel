<?php
/**
 * @author Jonathan Oddy <jonathan@woaf.net>
 * @copyright 2012 Jonathan Oddy
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Serialization;

class ClassSerialization
{

    const INCLUDE_ALWAYS = 1;
    const INCLUDE_NON_DEFAULT = 2;
    const INCLUDE_NON_EMPTY = 3;
    const INCLUDE_NON_NULL = 4;

    /**
     * @var \Weasel\JsonMarshaller\Config\Serialization\PropertySerialization[]
     */
    public $properties = array();

    public $include = self::INCLUDE_ALWAYS;

    /**
     * @var \Weasel\JsonMarshaller\Config\Serialization\TypeInfo
     */
    public $typeInfo;
}
