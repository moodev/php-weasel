<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
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
     * @var \Weasel\JsonMarshaller\Config\Serialization\PropertySerialization[] Map of names to how to serialize them
     */
    public $properties = array();

    public $include = self::INCLUDE_ALWAYS;

    /**
     * @var \Weasel\JsonMarshaller\Config\Serialization\TypeInfo
     */
    public $typeInfo;

    public $anyGetter = null;

    public function __toString()
    {
        $props = array();
        foreach ($this->properties as $key => $value) {
            $props[] = $key . ' => ' . $value;
        }
        return '[ClassSerialization include=' . $this->include . ' anyGetter=' . $this->anyGetter . ' typeInfo=' . $this->typeInfo .
            ' properties={' . implode(", ", $props) . '}';
    }
}
