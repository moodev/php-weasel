<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Serialization;

abstract class PropertySerialization
{

    /**
     * @var integer
     */
    public $include;

    /**
     * @var string
     */
    public $type;

    /**
     * @var \Weasel\JsonMarshaller\Config\Serialization\TypeInfo
     */
    public $typeInfo;
}
