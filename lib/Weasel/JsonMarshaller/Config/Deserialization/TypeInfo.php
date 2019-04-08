<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Deserialization;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;

class TypeInfo
{
    const TI_USE_CLASS = 1;
    const TI_USE_CUSTOM = 2;
    const TI_USE_MINIMAL_CLASS = 3;
    const TI_USE_NAME = 4;
    const TI_USE_NONE = 5;

    const TI_AS_PROPERTY = 1;
    const TI_AS_WRAPPER_OBJECT = 2;
    const TI_AS_WRAPPER_ARRAY = 3;
    const TI_AS_EXTERNAL_PROPERTY = 4;

    /**
     * @var string[]
     * @JsonProperty(type="string[string]")
     */
    public $subTypes;

    /**
     * @var int
     * @JsonProperty(type="int")
     */
    public $typeInfo = self::TI_USE_NONE;

    /**
     * @var int
     * @JsonProperty(type="int")
     */
    public $typeInfoAs = self::TI_AS_PROPERTY;

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $defaultImpl;

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $typeInfoProperty;

    /**
     * @var bool
     * @JsonProperty(type="bool")
     */
    public $typeInfoVisible = false;

    public function __toString()
    {
        return '[TypeInfo subTypes={' . implode(', ', $this->subTypes) . '} typeInfo=' . $this->typeInfo .
        ' typeInfoAs=' . $this->typeInfoAs . ' typeInfoProperty=' . $this->typeInfoProperty .
        'defaultImpl=' . $this->defaultImpl . ' typeInfoVisible=' . ($this->typeInfoVisible ? "true" : "false") . ']';
    }
}
