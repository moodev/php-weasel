<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\Serialization;

class ClassSerialization
{

    const INCLUDE_ALWAYS = 1;
    const INCLUDE_NON_DEFAULT = 2;
    const INCLUDE_NON_EMPTY = 3;
    const INCLUDE_NON_NULL = 4;

    /**
     * @var \Weasel\XmlMarshaller\Config\Serialization\PropertySerialization[]
     */
    public $properties = array();

    public $include = self::INCLUDE_ALWAYS;

    /**
     * @var \Weasel\XmlMarshaller\Config\Serialization\TypeInfo
     */
    public $typeInfo;
}
