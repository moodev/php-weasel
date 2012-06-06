<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\Deserialization;

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
     */
    public $subTypes;

    /**
     * @var int
     */
    public $typeInfo = self::TI_USE_NONE;

    /**
     * @var int
     */
    public $typeInfoAs = self::TI_AS_PROPERTY;

    /**
     * @var string
     */
    public $defaultImpl;

    /**
     * @var string
     */
    public $typeInfoProperty;

    /**
     * @var bool
     */
    public $typeInfoVisible = false;
}
